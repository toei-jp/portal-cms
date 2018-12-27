<?php
/**
 * EditorController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Controller\API;

use Toei\PortalAdmin\Form;
use Toei\PortalAdmin\Form\API as ApiForm;

/**
 * Editor API controller
 */
class EditorController extends BaseController
{
    /**
     * Blob Container name
     *
     * @var string
     */
    protected $blobContainer = 'editor';
    
    /**
     * upload action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeUpload($request, $response, $args)
    {
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
        $form = new ApiForm\EditorUploadForm();
        $form->setData($params);
        
        if (!$form->isValid()) {
            $errors = [];
            $messages = $form->getMessages()['file'];
            
            foreach ($messages as $message) {
                $errors[] = [
                    'title' => $message,
                ];
            }
            
            $this->data->set('errors', $errors);
            return;
        }
        
        $cleanData = $form->getData();
        
        $file = $cleanData['file'];
        
        // rename
        $info = pathinfo($file['name']);
        $blobName = md5(uniqid('', true)) . '.' . $info['extension'];
        
        // upload storage
        $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
        $options->setContentType($file['type']);
        $this->bc->createBlockBlob(
            $this->blobContainer,
            $blobName,
            fopen($file['tmp_name'], 'r'),
            $options);
        
        $url = $this->createBlobUrl($this->blobContainer, $blobName);
        
        $data = [
            'name' => $blobName,
            'url'  => $url,
        ];
        
        $this->data->set('data', $data);
    }
    
    /**
     * create Blob URL
     * 
     * Blobへのpublicアクセスを許可する必要があります。
     *
     * @param string $container
     * @param string $blob
     * @return string
     */
    protected function createBlobUrl(string $container, string $blob)
    {
        $settings = $this->container->get('settings')['storage'];
        $protocol = $settings['secure'] ? 'https' : 'http';
        
        return sprintf(
            '%s://%s.blob.core.windows.net/%s/%s',
            $protocol,
            $settings['account']['name'],
            $container,
            $blob);
    }
}
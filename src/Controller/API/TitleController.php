<?php
/**
 * TitleController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Controller\API;

use Toei\PortalAdmin\ORM\Entity;
use Toei\PortalAdmin\Form;

/**
 * Title API controller
 */
class TitleController extends BaseController
{
    /**
     * list action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeList($request, $response, $args)
    {
        $name = $request->getParam('name');
        $data = [];
        
        if (!empty($name)) {
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForListApi($name);
            
                
            foreach ($titles as $title) {
                /** @var Entity\Title $title */
                
                $data[] = [
                    'id'            => $title->getId(),
                    'name'          => $title->getName(),
                    'official_site' => $title->getOfficialSite(),
                    'publishing_expected_date' => $title->getPublishingExpectedDate()
                                               ? $title->getPublishingExpectedDate()->format('Y/m/d')
                                               : null,
                ];
            }
        }
        
        $this->data->set('data', $data);
    }

    /**
     * find imported title action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeFindImported($request, $response, $args)
    {
        $ids = json_decode($request->getParam('ids'));
        $data = [];
        
        if ($ids !== null) {
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForFindImportedApi($ids);
            
            foreach ($titles as $title) {
                /** @var Entity\Title $title */
                
                array_push($data, $title->getCheverCode());
            }
        }
        
        $this->data->set('data', $data);
    }

    /**
     * import title action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeImportTitles($request, $response, $args)
    {
        /**@var Entity\Title $title */
        $data = $request->getParams();
        $errors = [];
        foreach ($data['titles'] as $title) {
            $form = new Form\TitleForm();
            $ratings = $form->getRatingChoices();
            $i = array_search($title['rating'], $ratings);
            if ($i !== false) {
                $title['rating'] = $i;
            } else {
                unset($title['rating']);
            }
            $params = Form\BaseForm::buildData($title, []);
            $form->setData($params);
            
            if (!$form->isValid()) {
                array_push($errors, $form->getMessages());
            } else {
                $cleanData = $form->getData();
                $title = new Entity\Title();
                $title->setName($cleanData['name']);
                if (!empty($cleanData['sub_title'])) {
                    $title->setSubTitle($cleanData['sub_title']);
                }
                $title->setCheverCode($cleanData['chever_code']);
                $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
                $title->setRating((int) $cleanData['rating']);
                $title->setUniversal([]);
                $title->setCreatedUser($this->auth->getUser());
                $title->setUpdatedUser($this->auth->getUser());
                
                $this->em->persist($title);
                $this->em->flush();
            }
        }
        if (count($errors) > 0) {
            $this->data->set('status', 'fail');
            $this->data->set('errors', $errors);
        } else {
            $this->data->set('status', 'success');
        }
    }
    
    /**
     * autocomplete action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeAutocomplete($request, $response, $args)
    {
        $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForAutocomplete($request->getParams());
                
        $data = [];
        
        foreach ($titles as $title) {
            /** @var Entity\Title $title */
            
            $data[] = [
                'name'      => $title->getName(),
                'name_kana' => $title->getNameKana(),
                'sub_title' => $title->getSubTitle(),
            ];
        }
        
        $this->data->set('data', $data);
    }
}

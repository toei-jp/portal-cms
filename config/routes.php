<?php

/**
 * routes.php
 */

use App\Controller\AdminUserController;
use App\Controller\AuthController;
use App\Controller\AdvanceTicketController;
use App\Controller\CampaignController;
use App\Controller\IndexController;
use App\Controller\MainBannerController;
use App\Controller\NewsController;
use App\Controller\ScheduleController;
use App\Controller\TheaterMetaController;
use App\Controller\TitleController;
use App\Controller\API\AuthController as AuthApiController;
use App\Controller\API\CampaignController as CampaignApiController;
use App\Controller\API\EditorController as EditorApiController;
use App\Controller\API\MainBannerController as MainBannerApiController;
use App\Controller\API\NewsController as NewsApiController;
use App\Controller\API\TitleController as TitleApiController;
use App\Middleware\AuthMiddleware;

// phpcs:disable SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration
/** @var \Slim\App $app */
// phpcs:enable

$app->get('/login', AuthController::class . ':login')->setName('login');
$app->post('/auth', AuthController::class . ':auth')->setName('auth');
$app->get('/logout', AuthController::class . ':logout')->setName('logout');

$app->group('', function () {
    $this->get('/', IndexController::class . ':index')->setName('homepage');

    $this->group('/title', function () {
        $this->get('/list', TitleController::class . ':list')->setName('title_list');
        $this->get('/new', TitleController::class . ':new')->setName('title_new');
        $this->get('/import', TitleController::class . ':import')->setName('title_import');
        $this->post('/create', TitleController::class . ':create')->setName('title_create');
        $this->get('/{id}/edit', TitleController::class . ':edit')->setName('title_edit');
        $this->post('/{id}/update', TitleController::class . ':update')->setName('title_update');
        $this->get('/{id}/delete', TitleController::class . ':delete')->setName('title_delete');
    });

    $this->group('/schedule', function () {
        $this->get('/list', ScheduleController::class . ':list')->setName('schedule_list');
        $this->get('/new', ScheduleController::class . ':new')->setName('schedule_new');
        $this->post('/create', ScheduleController::class . ':create')->setName('schedule_create');
        $this->get('/{id}/edit', ScheduleController::class . ':edit')->setName('schedule_edit');
        $this->post('/{id}/update', ScheduleController::class . ':update')->setName('schedule_update');
        $this->get('/{id}/delete', ScheduleController::class . ':delete')->setName('schedule_delete');
    });

    $this->group('/main_banner', function () {
        $this->get('/publication', MainBannerController::class . ':publication')->setName('main_banner_publication');
        $this->post('/publication/update/{target}', MainBannerController::class . ':publicationUpdate')
            ->setName('main_banner_publication_update');
        $this->get('/list', MainBannerController::class . ':list')->setName('main_banner_list');
        $this->get('/new', MainBannerController::class . ':new')->setName('main_banner_new');
        $this->post('/create', MainBannerController::class . ':create')->setName('main_banner_create');
        $this->get('/{id}/edit', MainBannerController::class . ':edit')->setName('main_banner_edit');
        $this->post('/{id}/update', MainBannerController::class . ':update')->setName('main_banner_update');
        $this->get('/{id}/delete', MainBannerController::class . ':delete')->setName('main_banner_delete');
    });

    $this->group('/campaign', function () {
        $this->get('/publication', CampaignController::class . ':publication')->setName('campaign_publication');
        $this->post('/publication/update/{target}', CampaignController::class . ':publicationUpdate')
            ->setName('campaign_publication_update');
        $this->get('/list', CampaignController::class . ':list')->setName('campaign_list');
        $this->get('/new', CampaignController::class . ':new')->setName('campaign_new');
        $this->post('/create', CampaignController::class . ':create')->setName('campaign_create');
        $this->get('/{id}/edit', CampaignController::class . ':edit')->setName('campaign_edit');
        $this->post('/{id}/update', CampaignController::class . ':update')->setName('campaign_update');
        $this->get('/{id}/delete', CampaignController::class . ':delete')->setName('campaign_delete');
    });

    $this->group('/news', function () {
        $this->get('/publication', NewsController::class . ':publication')->setName('news_publication');
        $this->post('/publication/update/{target}', NewsController::class . ':publicationUpdate')
            ->setName('news_publication_update');
        $this->get('/list', NewsController::class . ':list')->setName('news_list');
        $this->get('/new', NewsController::class . ':new')->setName('news_new');
        $this->post('/create', NewsController::class . ':create')->setName('news_create');
        $this->get('/{id}/edit', NewsController::class . ':edit')->setName('news_edit');
        $this->post('/{id}/update', NewsController::class . ':update')->setName('news_update');
        $this->get('/{id}/delete', NewsController::class . ':delete')->setName('news_delete');
    });

    $this->group('/advance_ticket', function () {
        $this->get('/list', AdvanceTicketController::class . ':list')->setName('advance_ticket_list');
        $this->get('/new', AdvanceTicketController::class . ':new')->setName('advance_ticket_new');
        $this->post('/create', AdvanceTicketController::class . ':create')->setName('advance_ticket_create');
        $this->get('/{id}/edit', AdvanceTicketController::class . ':edit')->setName('advance_ticket_edit');
        $this->post('/{id}/update', AdvanceTicketController::class . ':update')->setName('advance_ticket_update');
        $this->get('/{id}/delete', AdvanceTicketController::class . ':delete')->setName('advance_ticket_delete');
    });

    $this->group('/theater', function () {
        $this->get('/opening-hour', TheaterMetaController::class . ':openingHour')->setName('opening_hour');
        $this->get('/{id}/opening-hour/edit', TheaterMetaController::class . ':openingHourEdit')
            ->setName('opening_hour_edit');
        $this->post('/{id}/opening-hour/update', TheaterMetaController::class . ':openingHourUpdate')
            ->setName('opening_hour_update');
    });

    $this->group('/admin_user', function () {
        $this->get('/list', AdminUserController::class . ':list')->setName('admin_user_list');
        $this->get('/new', AdminUserController::class . ':new')->setName('admin_user_new');
        $this->post('/create', AdminUserController::class . ':create')->setName('admin_user_create');
    });

    $this->group('/api', function () {
        $this->group('/title', function () {
            $this->get('/list', TitleApiController::class . ':list');
            $this->get('/autocomplete', TitleApiController::class . ':autocomplete');
            $this->get('/findImported', TitleApiController::class . ':findImported');
            $this->post('/importTitles', TitleApiController::class . ':importTitles');
        });

        $this->group('/main_banner', function () {
            $this->get('/list', MainBannerApiController::class . ':list');
        });

        $this->group('/campaign', function () {
            $this->get('/list', CampaignApiController::class . ':list');
        });

        $this->group('/news', function () {
            $this->get('/list', NewsApiController::class . ':list');
        });

        $this->group('/editor', function () {
            $this->post('/upload', EditorApiController::class . ':upload');
        });

        $this->group('/auth', function () {
            $this->post('/token', AuthApiController::class . ':token');
        });
    });
})->add(new AuthMiddleware($container));

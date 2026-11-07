<?php

namespace Youvision\ThinkPinyin;

use think\Service as ThinkService;
use Youvision\ThinkPscws4\Pscws4Manager;

class Service extends ThinkService
{
    public function register(): void
    {
        $defaults = require __DIR__ . '/../config/pinyin.php';
        $config = (array) $this->app->config->get('pinyin', []);

        $this->app->config->set(array_merge($defaults, $config), 'pinyin');

        $app = $this->app;

        $this->app->bind('pinyin', function () use ($app) {
            $pscws4 = new Pscws4Manager(
                (array) $app->config->get('pscws4', []),
                $app->getRootPath()
            );

            $app->instance('pscws4', $pscws4);
            $app->instance(Pscws4Manager::class, $pscws4);

            return new PinyinManager($pscws4, (array) $app->config->get('pinyin', []));
        });

        $this->app->bind(PinyinManager::class, function () use ($app) {
            return $app->make('pinyin');
        });
    }
}
<?php

namespace Youvision\ThinkPinyin;

use think\Facade as ThinkFacade;

/**
 * @method static string[] words(string $text)
 * @method static string cut(string $text, ?string $separator = null)
 * @method static string slug(string $text, ?string $separator = null)
 * @method static string permalink(string $text, ?string $separator = null)
 * @method static string sentence(string $text, ?string $separator = null)
 * @method static string abbr(string $text)
 * @method static array config()
 *
 * @see \Youvision\ThinkPinyin\PinyinManager
 */
class Facade extends ThinkFacade
{
    protected static function getFacadeClass(): string
    {
        return PinyinManager::class;
    }
}
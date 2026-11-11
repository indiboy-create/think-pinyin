## ThinkPHP-PanGu - 盘古ThinkPHP开源索引!
面向 ThinkPHP 的开源工程索引：组件、框架、服务与发布工具，按能力组织，持续整理。
仓库地址：**<https://github.com/indiboy-create/ThinkPHP-PanGu/>**
在线站点：**<https://indiboy-create.github.io/ThinkPHP-PanGu/>**

# think-pinyin

基于 [PSCWS4](https://github.com/indiboy-create/think-pscws4) 中文分词与 [overtrue/pinyin](https://github.com/overtrue/pinyin) 的中文转拼音工具，适配 ThinkPHP 8。

先用 PSCWS4 把内容拆成词语，把词语用 `-` 连接后再逐词转换为拼音。逐词转换能让多音字在词语环境中被正确识别，例如「重庆」输出 `chongqing` 而不是 `zhongqing`。

## 安装

```bash
composer require youvision/think-pinyin
```

服务通过 `extra.think.services` 自动注册，无需手动添加。

## 配置

包内默认配置位于 `config/pinyin.php`，可在应用的 `config/pinyin.php` 中覆盖：

```php
return [
    'split'            => true,     // 是否先分词再转拼音
    'separator'        => '-',      // 词与词之间的连接符
    'word_separator'   => '',       // 词内各字拼音的连接符，留空为连写
    'tone'             => 'none',   // symbol / number / none
    'yu'               => 'v',      // ü 的写法：v / yu / u
    'keep_non_chinese' => true,     // 是否保留英文、数字
    'strip_tags'       => true,     // 是否剥离 HTML 标签
    'lowercase'        => true,     // 是否转小写
    'max_length'       => 0,        // 最大字符数，0 不限制
    'strategy'         => 'memory', // memory / cached / smart / auto
];
```

## 使用

门面：

```php
use Youvision\ThinkPinyin\Facade;

// 分词结果转拼音（分词后用 - 连接，再逐词转拼音）
echo Facade::slug('中国航天官员应邀到美国开会');
// zhongguohangtian-guanyuan-yingyao-dao-meiguo-kaihui

// 仅分词，不转拼音
echo Facade::cut('中国航天官员应邀到美国开会');
// 中国-航天-官员-应邀-到-美国-开会

// 整段文字转拼音
echo Facade::sentence('带着希望去旅行');
// dai zhe xi wang qu lv xing

// 首字母
echo Facade::abbr('带着希望去旅行');
// dzxwqlx

// 词语列表
Facade::words('中国航天官员应邀到美国开会');
// ['中国航天', '官员', '应邀', '到', '美国', '开会']
```

容器注入：

```php
use Youvision\ThinkPinyin\PinyinManager;

class Article
{
    public function __construct(protected PinyinManager $pinyin)
    {
    }

    public function slug(string $title): string
    {
        return $this->pinyin->slug($title);
    }
}
```

单次调用可覆盖连接符：

```php
Facade::slug('中国航天 官员', '_'); // zhongguohangtian_guanyuan
```

## 方法

| 方法 | 说明 |
| --- | --- |
| `words(string $text): array` | 返回分词结果 |
| `cut(string $text, ?string $separator = null): string` | 分词后以连接符拼接 |
| `slug(string $text, ?string $separator = null): string` | 分词、转拼音并拼接，用于链接 |
| `permalink(string $text, ?string $separator = null): string` | `slug` 的别名 |
| `sentence(string $text, ?string $separator = null): string` | 整段文字转拼音 |
| `abbr(string $text): string` | 首字母字符串 |
| `config(): array` | 当前生效配置 |

## 依赖说明

- 分词依赖 [youvision/think-pscws4](https://github.com/indiboy-create/think-pscws4)，词典文件由其包内 `etc/dict.xdb` 提供。
- 拼音转换依赖 `overtrue/pinyin:^6.0`，要求 PHP 8.1 及以上。

## 许可证

MIT
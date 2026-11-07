<?php

namespace Youvision\ThinkPinyin;

use Overtrue\Pinyin\Collection;
use Overtrue\Pinyin\Contracts\ConverterInterface;
use Overtrue\Pinyin\Pinyin;
use Youvision\ThinkPscws4\Pscws4Manager;

class PinyinManager
{
    protected array $config;

    public function __construct(protected Pscws4Manager $pscws4, array $config = [])
    {
        $defaults = require __DIR__ . '/../config/pinyin.php';

        $this->config = array_merge($defaults, $config);

        $this->applyStrategy();
    }

    /**
     * 分词，返回词语列表。
     *
     * @return string[]
     */
    public function words(string $text): array
    {
        $words = $this->pscws4->words($this->normalize($text));

        return array_values(array_filter($words, fn ($word) => trim($word) !== ''));
    }

    /**
     * 分词后以连接符拼接，不做拼音转换。
     */
    public function cut(string $text, ?string $separator = null): string
    {
        return implode($separator ?? (string) $this->config['separator'], $this->words($text));
    }

    /**
     * 分词后用连接符拼接，再逐词转拼音，生成用于链接的拼音字符串。
     */
    public function slug(string $text, ?string $separator = null): string
    {
        $separator = $separator ?? (string) $this->config['separator'];
        $text = $this->normalize($text);

        if ($text === '') {
            return '';
        }

        $parts = [];

        if ($this->config['split']) {
            $segmented = $this->cut($text, $separator);
            $words = $separator === '' ? [$segmented] : explode($separator, $segmented);

            foreach ($words as $word) {
                $part = $this->convertWord($word);
                if ($part !== '') {
                    $parts[] = $part;
                }
            }
        } else {
            foreach ($this->convert($text)->all() as $item) {
                $item = $this->clean((string) $item);
                if ($item !== '') {
                    $parts[] = $item;
                }
            }
        }

        $slug = implode($separator, $parts);

        if ($this->config['lowercase']) {
            $slug = mb_strtolower($slug);
        }

        $maxLength = (int) $this->config['max_length'];
        if ($maxLength > 0 && mb_strlen($slug) > $maxLength) {
            $slug = $this->truncate($slug, $maxLength, $separator);
        }

        return trim($slug, $separator);
    }

    /**
     * slug 的别名。
     */
    public function permalink(string $text, ?string $separator = null): string
    {
        return $this->slug($text, $separator);
    }

    /**
     * 整段文字转拼音，按拼音单元拼接。
     */
    public function sentence(string $text, ?string $separator = null): string
    {
        $parts = [];

        foreach ($this->convert($this->normalize($text))->all() as $item) {
            $item = $this->clean((string) $item);
            if ($item !== '') {
                $parts[] = $item;
            }
        }

        return implode($separator ?? ' ', $parts);
    }

    /**
     * 获取首字母字符串。
     */
    public function abbr(string $text): string
    {
        return $this->clean(Pinyin::abbr($this->normalize($text))->join(''));
    }

    public function config(): array
    {
        return $this->config;
    }

    protected function convertWord(string $word): string
    {
        $word = $this->clean($word);

        if ($word === '') {
            return '';
        }

        if (! preg_match('/\p{Han}/u', $word)) {
            return $this->config['keep_non_chinese'] ? $word : '';
        }

        return $this->convert($word)->join((string) $this->config['word_separator']);
    }

    protected function truncate(string $slug, int $maxLength, string $separator): string
    {
        if ($separator === '') {
            return rtrim(mb_substr($slug, 0, $maxLength));
        }

        $result = '';

        foreach (explode($separator, $slug) as $segment) {
            $candidate = $result === '' ? $segment : $result . $separator . $segment;

            if (mb_strlen($candidate) > $maxLength) {
                break;
            }

            $result = $candidate;
        }

        return $result !== '' ? $result : mb_substr($slug, 0, $maxLength);
    }

    protected function convert(string $text): Collection
    {
        return $this->converter()
            ->withToneStyle((string) $this->config['tone'])
            ->convert($text);
    }

    protected function converter(): ConverterInterface
    {
        $converter = Pinyin::converter();

        if ((string) $this->config['tone'] === 'symbol') {
            return $converter;
        }

        return match ((string) $this->config['yu']) {
            'v'     => $converter->yuToV(),
            'yu'    => $converter->yuToYu(),
            'u'     => $converter->yuToU(),
            default => $converter,
        };
    }

    protected function applyStrategy(): void
    {
        match ((string) $this->config['strategy']) {
            'cached' => Pinyin::useCached(),
            'smart'  => Pinyin::useSmart(),
            'auto'   => Pinyin::useAutoStrategy(),
            default  => Pinyin::useMemoryOptimized(),
        };
    }

    protected function normalize(string $text): string
    {
        if ($this->config['strip_tags']) {
            $text = (string) preg_replace('/<[^>]*>/u', ' ', $text);
        }

        $text = trim($text);

        return (string) preg_replace('/\s+/u', ' ', $text);
    }

    protected function clean(string $value): string
    {
        return trim((string) preg_replace('/[^\p{Han}\p{L}\p{N}]+/u', '', $value));
    }
}
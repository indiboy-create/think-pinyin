<?php
// +----------------------------------------------------------------------
// | think-pinyin 拼音转换配置
// +----------------------------------------------------------------------

return [
    // 是否启用分词：true 先用 PSCWS4 分词再逐词转拼音（多音字识别更准确），false 直接整段转拼音
    'split'          => true,

    // 词与词之间的连接符，例如 zhongguo-hangtian
    'separator'      => '-',

    // 词内各字拼音之间的连接符，留空表示词内连写，例如 zhongguo；设为 "-" 则输出 zhong-guo
    'word_separator' => '',

    // 拼音声调风格：symbol 声调符号（zhōng guó）、number 数字声调（zhong1 guo2）、none 无声调（zhong guo）
    'tone'           => 'none',

    // ü 的书写方式：v（lv）、yu（lyu）、u（lu）；声调风格为 symbol 时不生效
    'yu'             => 'v',

    // 是否保留英文、数字等非中文内容；false 时仅输出中文对应的拼音
    'keep_non_chinese' => true,

    // 是否先剥离 HTML 标签，标题内容带有富文本标签时建议开启
    'strip_tags'     => true,

    // 是否将最终结果转为小写，英文缩写等需要大写时设为 false
    'lowercase'      => true,

    // 结果最大字符数，0 表示不限制；超长时按完整拼音分段截断，不会切断单个拼音
    'max_length'     => 0,

    // 拼音转换策略：memory 内存优先（默认）、cached 缓存优先（批量处理更快）、smart 智能、auto 自动
    'strategy'       => 'memory',
];
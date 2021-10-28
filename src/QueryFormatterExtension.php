<?php
/**
 * Cycle Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Cycle\Bundle\Database;

use Doctrine\SqlFormatter\Highlighter;
use Doctrine\SqlFormatter\HtmlHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class QueryFormatterExtension extends AbstractExtension
{
    /**
     * @var SqlFormatter
     */
    private $sqlFormatter;

    public function __construct()
    {
        $this->sqlFormatter = new SqlFormatter(new HtmlHighlighter([
            HtmlHighlighter::HIGHLIGHT_PRE        => 'class="highlight highlight-sql"',
            Highlighter::HIGHLIGHT_QUOTE          => 'class="string"',
            Highlighter::HIGHLIGHT_BACKTICK_QUOTE => 'class="string"',
            Highlighter::HIGHLIGHT_RESERVED       => 'class="keyword"',
            Highlighter::HIGHLIGHT_BOUNDARY       => 'class="symbol"',
            Highlighter::HIGHLIGHT_NUMBER         => 'class="number"',
            Highlighter::HIGHLIGHT_WORD           => 'class="word"',
            Highlighter::HIGHLIGHT_ERROR          => 'class="error"',
            Highlighter::HIGHLIGHT_COMMENT        => 'class="comment"',
            Highlighter::HIGHLIGHT_VARIABLE       => 'class="variable"',
        ]));
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('cycle_prettify_sql', [$this, 'prettifySql'], ['is_safe' => ['html']]),
            new TwigFilter('cycle_format_sql', [$this, 'formatSql'], ['is_safe' => ['html']]),
        ];
    }

    public function prettifySql(string $sql): string
    {
        return $this->sqlFormatter->highlight($sql);
    }

    public function formatSql(string $sql): string
    {
        return $this->sqlFormatter->format($sql);
    }
}

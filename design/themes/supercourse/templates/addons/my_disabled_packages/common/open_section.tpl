{assign var="id" value=$section_title|md5|string_format:"s_%s"}
{math equation="rand()" assign="rnd"}

<div class="ty-section{if $class} {$class}{/if}" id="ds_{$rnd}">
    <div  class="ty-section__title" id="sw_{$id}">
        <span>{$section_title nofilter}</span>
    </div>
    <div id="{$id}" class="{$section_body_class|default:"ty-section__body"}">{$section_content nofilter}</div>
</div>
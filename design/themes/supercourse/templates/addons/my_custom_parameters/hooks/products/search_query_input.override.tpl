
<div class="ty-control-group">
    <label for="match" class="ty-control-group__title">{__("find_results_with")}</label>
    <select name="match" id="match">
        <option {if $search.match == "any"}selected="selected"{/if} value="any">{__("any_words")}</option>
        <option {if $search.match == "all"}selected="selected"{/if} value="all">{__("all_words")}</option>
        <option {if $search.match == "exact"}selected="selected"{/if} value="exact">{__("exact_phrase")}</option>
    </select>&nbsp;&nbsp;
    <input type="text" name="q" size="38" value="{$search.q}" class="ty-search-form__input ty-search-form__input-large" />
</div>
<input type="hidden" name="pname" value="Y" />


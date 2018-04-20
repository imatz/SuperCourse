<div style="margin-left: 20px;">
<!--<p>{__("onoma_print")}</p>
<br>
<form method="post" action="{""|fn_url}">
<select name="onoma">
  <option value="yes">Ναι</option>
  <option value="no">Όχι</option>
</select>

<br>
<br>-->
<p>{__("classroom_print")}</p><br>
<select name="classroom">
  <option value="Pre-Junior">Pre-Junior</option>
  <option value="A Junior">A Junior</option>
  <option value="B Junior">B Junior</option>
  <option value="Junior A+B">Junior A+B</option>
  <option value="A Senior">A Senior</option>
  <option value="B Senior">B Senior</option>
  <option value="C Senior">C Senior</option>
  <option value="D Senior">D Senior</option>
  <option value="B1">B1</option>
  <option value="B2">B2</option>
  <option value="C1">C1</option>
  <option value="C2">C2</option>
  <option value="Adults">Adults</option>
</select>
<br>
<br>
{$prod_id = $product.product_id}
{$smarty.post.$prod_id}
{include file="buttons/button.tpl" but_text=__("print_code") but_name="dispatch[my_changes.my_print.$prod_id]" but_role="select" but_meta="ty-btn__secondary"}
</form>
</div>
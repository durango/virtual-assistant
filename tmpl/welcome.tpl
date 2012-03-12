{* Welcome file *}
{extends file="layout.tpl"}
{block name="content"}
"Nice to meet you {$guestName|escape}!<br>
Who are you here to see?"<br><br>
<form class="form-horizontal" method="post" action="/?path=more">
  <input type="hidden" name="guestName" value="{$guestName|escape}">
  <fieldset>
    <legend>Employee Directory</legend>
    <div class="control-group">
      <label class="control-label" for="employee">Employee:</label>
      <div class="controls">
        <select name="employee" id="employee">
          {foreach from=$employees key=k item=employee}
            <option value="{$employee.id}">{$employee.name|escape}</option>
          {/foreach}
        </select>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Next</button>
    </div>
  </fieldset>
</form>
{/block}
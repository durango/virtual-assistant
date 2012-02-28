{* Welcome file *}
{extends file="layout.tpl"}
{block name="content"}
"Nice to meet you {$guestName|escape}!<br>
Who are you here to see?"<br><br>
<form class="form-horizontal" method="post" action="/?path=welcome">
  <fieldset>
    <legend>Employee Directory</legend>
    <div class="control-group">
      <label class="control-label" for="employee">Employee:</label>
      <div class="controls">
        <select name="employee" id="employee">
          <option value="1">Daniel Durante</option>
          <option value="2">Paulo Da Silva</option>
          <option value="3">Pavle Stojkovic</option>
        </select>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Next</button>
    </div>
  </fieldset>
</form>
{/block}
{* More file *}
{extends file="layout.tpl"}
{block name="content"}
<h2>{$employeeName|escape}</h2>
{if $jabber === true}
It looks like {$employeeName|escape} is at his desk.
{else}
It looks like {$employeeName|escape} is not at his desk.
{/if}
<br><br>
What are you here to talk about?<br><br>
<form id="send" action="?path=send" method="post" class="form-horizontal">
<input type="hidden" name="employee" value="{$employee}">
<input type="hidden" name="guestName" value="{$guestName|escape}">
  <fieldset>
    <legend>Topic</legend>
    <div class="control-group">
      <label class="control-label" for="employee">Message:</label>
      <div class="controls">
        <textarea id="msg" name="message" rows="8" class="text"></textarea>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Next</button>
    </div>
  </fieldset>
</form>
{/block}
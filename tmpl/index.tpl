{* Index file *}
{extends file="layout.tpl"}
{block name="content"}
"Hello, I'm Pickle. Welcome to Grooveshark!
<br>
What is your name?"<br><br>
<form class="form-horizontal" method="post" action="/?path=welcome">
  <fieldset>
    <legend>Virtual Assistant</legend>
    <div class="control-group">
      <label class="control-label" for="name">Your name:</label>
      <div class="controls">
        <input type="text" name="name" id="name" class="input-xlarge">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Next</button>
    </div>
  </fieldset>
</form>
{/block}
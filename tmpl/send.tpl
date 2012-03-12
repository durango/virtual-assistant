{* Send file *}
{extends file="layout.tpl"}
{block name="content"}
<h2>Message Sent</h2>
Sounds cool! I contacted {$employeeName|escape}.<br>
Please have a seat, {$employeeName|escape} will be with you shortly.
<script type="text/javascript">
$(document).ready(function(){
  window.setTimeout(function(){
    window.location = '/';
  }, 10000);
});
</script>
{/block}
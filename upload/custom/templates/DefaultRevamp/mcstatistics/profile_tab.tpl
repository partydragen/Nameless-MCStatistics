<h3 class="ui header">
  {$INGAME_STATISTICS_TITLE}
</h3>

{if isset($MCSTATISTICS_ERROR)}
  <div class="ui yellow info message">
    <div class="content">
      {$MCSTATISTICS_ERROR}
    </div>
  </div>
{else}
<div class="ui grid">
  {foreach from=$MCSTATISTICS_FIELDS key=key item=field}
    <div class="four wide column">
        <strong>{$field.title}</strong></br>
        <p>{$field.value}</p>
    </div>
  {/foreach}
</div>

</br>

<center>Statistics provided by <a href="https://mcstatistics.org/" target="_blank">MCStatistics</a></center>
{/if}
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

{foreach from=$MCSTATISTICS_SERVERS item=server}
  <h3>{$server.name}</h3>
  <hr>
  <div class="ui grid">
  {foreach from=$server.fields item=field}
    <div class="four wide column">
        <strong>{$field.title}</strong></br>
        <p>{$field.value}</p>
    </div>
  {/foreach}
  </div>
  </br>
{/foreach}


<center>Statistics provided by <a href="https://mcstatistics.org/" target="_blank">MCStatistics</a></center>
{/if}
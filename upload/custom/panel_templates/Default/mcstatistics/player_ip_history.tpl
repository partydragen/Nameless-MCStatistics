{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$USERNAME_VALUE}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$MCSTATISTICS}</li>
                        <li class="breadcrumb-item active">{$USERNAME_VALUE}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <!-- Success and Error Alerts -->
                {include file='includes/alerts.tpl'}

                <div class="row">
                    <div class="col-md-3">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="text-center">
                                    <img class="profile-user-img rounded-circle" src="{$AVATAR}" alt="{$USERNAME}">
                                </div>

                                <h4 class="text-center" style="{$USER_STYLE}">{$USERNAME_VALUE}</h4>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>{$REGISTERED}</b><br />{$REGISTERED_VALUE}
                                    </li>
                                    <li class="list-group-item">
                                        <b>{$LAST_SEEN}</b><br /><span data-toggle="tooltip" data-title="{$LAST_SEEN_FULL_VALUE}">{$LAST_SEEN_SHORT_VALUE}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>{$PLAY_TIME}</b><br />{$PLAY_TIME_VALUE}
                                    </li>
                                    <li class="list-group-item">
                                        <b>{$LAST_IP}</b><br />{$LAST_IP_VALUE}
                                    </li>
                                    <li class="list-group-item">
                                        <b>{$LAST_VERSION}</b><br />{$LAST_VERSION_VALUE}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{$DETAILS_LINK}">{$DETAILS}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{$SESSIONS_LINK}">{$SESSIONS}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active">{$IP_HISTORY}</a>
                                    </li>
                                </ul>
                                <hr />
                                            
                                {if count($IP_HISTORY_LIST)}
                                <div class="table-responsive">
                                  <table class="table table-striped">
                                    <thead>
                                      <tr>
                                        <th>{$IP_ADDRESS}</th>
                                        <th>{$SESSIONS}</th>
                                      </tr>
                                    </thead>
                                    <tbody id="sortable">
                                    {foreach from=$IP_HISTORY_LIST item=session}
                                      <tr data-id="{$session.id}">
                                        <td>{$session.ip}</td>
                                        <td>{$session.sessions} {$SESSIONS}</td>
                                      </tr>
                                    {/foreach}
                                    </tbody>
                                  </table>
                                </div>
                                {else}
                                <p>{$NO_DATA_AVAILABLE}</p>
                                {/if}
                                
                                <center><p>MCStatistics Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a> and my <a href="https://partydragen.com/supporters/" target="_blank">Sponsors</a></br>
                                Data provided by <a href="https://mcstatistics.org/" target="_blank">MCStatistics</a></p></center>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

</body>

</html>
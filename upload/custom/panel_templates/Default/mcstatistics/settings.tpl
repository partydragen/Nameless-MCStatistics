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
                    <h1 class="h3 mb-0 text-gray-800">{$MCSTATISTICS}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item">{$MCSTATISTICS}</li>
                            <li class="breadcrumb-item active">{$SETTINGS}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h3 style="display:inline;">{$SETTINGS}</h3>
                        <hr>
                        
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form action="" method="post">
                            <div class="form-group">
                                <label for="inputSecretKey">{$SECRET_KEY}</label>
                                <span class="badge badge-info" data-html="true" data-toggle="popover" title="{$INFO}" data-content="{$SECRET_KEY_INFO}"><i class="fas fa-question-circle"></i></span>
                                <input id="inputSecretKey" name="secret_key" class="form-control" placeholder="{$SECRET_KEY}" value="{$SECRET_KEY_VALUE}">
                            </div>
                            <div class="form-group">
                                <label for="link_location">{$LINK_LOCATION}</label>
                                <select class="form-control" id="link_location" name="link_location">
                                    <option value="1"{if $LINK_LOCATION_VALUE eq 1} selected{/if}>{$LINK_NAVBAR}</option>
                                    <option value="2"{if $LINK_LOCATION_VALUE eq 2} selected{/if}>{$LINK_MORE}</option>
                                    <option value="3"{if $LINK_LOCATION_VALUE eq 3} selected{/if}>{$LINK_FOOTER}</option>
                                    <option value="4"{if $LINK_LOCATION_VALUE eq 4} selected{/if}>{$LINK_NONE}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="leaderboard_link_location">{$LEADERBOARD_LINK_LOCATION}</label>
                                <select class="form-control" id="leaderboard_link_location" name="leaderboard_link_location">
                                    <option value="1"{if $LEADERBOARD_LINK_LOCATION_VALUE eq 1} selected{/if}>{$LINK_NAVBAR}</option>
                                    <option value="2"{if $LEADERBOARD_LINK_LOCATION_VALUE eq 2} selected{/if}>{$LINK_MORE}</option>
                                    <option value="3"{if $LEADERBOARD_LINK_LOCATION_VALUE eq 3} selected{/if}>{$LINK_FOOTER}</option>
                                    <option value="4"{if $LEADERBOARD_LINK_LOCATION_VALUE eq 4} selected{/if}>{$LINK_NONE}</option>
                                </select>
                            </div>
                            <div class="form-group custom-control custom-switch">
                                <input id="inputProfileStats" name="display_profile" type="checkbox" class="custom-control-input"{if $SHOW_STATS_ON_PROFILE_VALUE eq 1} checked{/if} />
                                <label class="custom-control-label" for="InputProfileStats">{$SHOW_STATS_ON_PROFILE}</label>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>
                        
                        <center><p>MCStatistics Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a> and my <a href="https://partydragen.com/supporters/" target="_blank">Sponsors</a></br>
                        Data provided by <a href="https://mcstatistics.org/" target="_blank">MCStatistics</a></p></center>
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
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
                                <label for="InputProfileStats">{$SHOW_STATS_ON_PROFILE}</label>
                                <input id="inputProfileStats" name="display_profile" type="checkbox" class="js-switch"{if $SHOW_STATS_ON_PROFILE_VALUE eq 1} checked{/if} />
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>
                        
                        <center><p>MCStatistics Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a></p></center>
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
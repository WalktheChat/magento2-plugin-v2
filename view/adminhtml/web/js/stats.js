define(['jquery', 'uiRegistry'],function ($, registry) {
    'use strict';

    return function (config) {
        var walkthechatStats = {
            init: function () {
                $.ajax({
                    url: config.url,
                    type: 'GET',
                    dataType: 'json',
                    complete: function (response) {
                        var stats = response.responseJSON;

                        $('#walkthechat_dashboard_stats_resync_status').html(stats.resync_status);
                        $('#walkthechat_dashboard_stats_synced_products').html(stats.synced_products);
                        $('#walkthechat_dashboard_stats_exported_products').html(stats.exported_products);
                        $('#walkthechat_dashboard_stats_synced_images').html(stats.synced_images);
                        $('#walkthechat_dashboard_stats_exported_images').html(stats.exported_images);
                    }
                });
            }
        };

        walkthechatStats.init();
    }
});

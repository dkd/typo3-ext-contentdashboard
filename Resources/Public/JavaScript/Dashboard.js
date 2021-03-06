define('TYPO3/CMS/Contentdashboard/Dashboard', [
    'jquery',
    'TYPO3/CMS/Contentdashboard/d3',
    'TYPO3/CMS/Contentdashboard/d3dateline',
    'TYPO3/CMS/Contentdashboard/d3datelineCallbacks'
], function (
    $
) {
    $(document).ready(function(){
        var spinner = '<div class="text-center"><i class="fa fa-spinner fa-spin"></i></div>';
        $('.object-detail-trigger').on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            var contentContainer = $($this.attr('href'));
            if($this.hasClass('opened')) {
                $this.removeClass('opened');
                $this.parents('.asset-brief').removeClass('active');
                contentContainer.hide();
            } else {
                if ($this.attr('data-href')) {
                    contentContainer.html(spinner);
                    contentContainer.show();
                    $.getJSON($this.attr('data-href'),function(data){
                        contentContainer.hide();
                        contentContainer.html(data.content);
                        $this.addClass('opened');
                        $this.parents('.asset-brief').addClass('active');
                        contentContainer.slideDown();

                        var lifeCycleGraph = $(contentContainer).find('.life-cycle-graph');
                        if(lifeCycleGraph.length == 1) {
                            var lifeCycleId = lifeCycleGraph.attr('id');
                            loadchart(lifeCycleGraph.attr('id'), lifeCycleGraph.data('url'));
                        }
                    });
                }
            }
        });
    });
    return {};
});

<script>
function drawChart(dataSet,id) {
	var ctx = document.getElementById("myChart-"+id).getContext("2d");
	var dataOptions = {
		    datasets: [{
		        data: dataSet.data,
		        backgroundColor: [
		                      '#f497a9',
		                      '#b6d884'
		                  ]
		    }],
		    // These labels appear in the legend and in the tooltips when hovering different arcs
		    labels: [
		        dataSet.range.pos+' - stars',
		        dataSet.range.neg+' - stars'	        
		    ]	    
		};		
	var myPieChart = new Chart(ctx,{
	    type: 'pie',
	    data: dataOptions,
	    options: {
		    legend: {
		    	  display: false
	    	},
	    	onClick : function(evt) {

		    	var activeElement = myPieChart.getElementAtEvent(evt);
		    	var dataClicked = dataOptions.datasets[activeElement[0]._datasetIndex].data[activeElement[0]._index];

		    	document.location = "<?php echo erLhcoreClassDesign::baseurl('survey/collected')?>/<?php echo $survey->id?>/(max_stars_"+id+")/"+dataSet.rangefilter[dataClicked].join("/");
		    }
	    }
	});
}
</script>

<?php foreach ($enabledStars as $starKey => $starEnabled) : 
$positiveRange = array($survey->{'max_stars_' . $starEnabled},ceil($survey->{'max_stars_' . $starEnabled}/2)+1);
$negativeRange = array(1,ceil($survey->{'max_stars_' . $starEnabled}/2));

$positiveChatsCount = $survey->getStarsNumberVotes($starEnabled,range($positiveRange[0], $positiveRange[1]));
$negativeChatsCount = $survey->getStarsNumberVotes($starEnabled,range($negativeRange[0], $negativeRange[1]));

$totalCount = $positiveChatsCount + $negativeChatsCount;
?>

<h1 class="text-center"><?php echo $starFields[$starKey]?></h1>

<div class="row">
    <div class="col-xs-3">
        <h2 class="text-center chat-active"><i class="material-icons">&#xE8DC;</i></h2>
        <h2 class="text-center"><?php echo $positiveChatsCount?></h2>
        <p class="text-center"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Great chats with average of')?> <?php echo $positiveRange[0],'-',$positiveRange[1];?></p>
        
        <h2 class="text-center chat-closed"><i class="material-icons">&#xE8DB;</i></h2>
        <h2 class="text-center"><?php echo $negativeChatsCount?></h2>
        <p class="text-center"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Not so great chats')?> <?php echo $negativeRange[0],'-',$negativeRange[1];?></p>
    </div>
        <div class="col-xs-5">
            <canvas id="myChart-<?php echo $starEnabled?>" width="400" height="300" style="cursor:pointer"></canvas>
        </div>
    <div class="col-xs-4">        
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Average')?> - <?php echo number_format(round($survey->getStarsNumberAverage($starEnabled)*100)/100,2)?></h4>        
        <table class="table table-condensed">
            <?php for ($i = $survey->{'max_stars_' . $starEnabled}; $i >= 1; $i--) : ?>
            <tr>
                <td width="1%" nowrap>
                
                <a href="<?php echo erLhcoreClassDesign::baseurl('survey/collected')?>/<?php echo $survey->id?>/(max_stars_<?php echo $starEnabled?>)/<?php echo $i?>"><?php echo $i?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Stars')?></a>
                
                </td>
                <td>
                <?php
                if ($totalCount > 0) : $percentange = round(($survey->getStarsNumberVotes($starEnabled,array($i))/$totalCount*100));?>
                    <div class="progress" style="margin-bottom:0">
                        <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentange?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percentange?>%">
                            <span class="sr-only"></span>
                        </div>
                    </div>
                <?php else :
                    echo '-';
                endif;
                ?>
                </td>
            </tr>
            <?php endfor;?>
        </table>
    </div>
</div>

<script>
drawChart({rangefilter:{<?php echo $positiveChatsCount?> : <?php echo json_encode(range($positiveRange[0], $positiveRange[1]))?>,<?php echo $negativeChatsCount?> : <?php echo json_encode(range($negativeRange[0], $negativeRange[1]))?>},data:[<?php echo $negativeChatsCount?>,<?php echo $positiveChatsCount?>],range:{pos:'<?php echo $negativeRange[0],'-',$negativeRange[1]?>',neg:'<?php echo $positiveRange[0],'-',$positiveRange[1]?>'}},<?php echo $starEnabled?>);
</script>
<hr>
<?php endforeach; ?>


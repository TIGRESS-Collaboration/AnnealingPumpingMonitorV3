<!DOCTYPE html>
<html>
	<head>
		<title id="title">GRSPi02 Annealing Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="utf-8">
		<link rel="stylesheet" 
		href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="scripts/index.js"></script>
    <script>
        
			$(document).ready(function(){
				$("#configForm").submit(function(event){
					submitForm();
					return false;
				});
			});
			$(document).ready(function(){
				$("#calibFormT").submit(function(event){
					submitFormT();
					formSubUpdate();
					return false;
				});
			});
			$(document).ready(function(){
				$("#calibFormP").submit(function(event){
					submitFormP();
					formSubUpdate();
					return false;
				});
			});

			$(document).ready(function(){
				$("#sampleRateForm").submit(function(event){
					submitFormSampleRate();
					return false;
				});
			});
			$(document).ready(function(){
				$("#tc2Form").submit(function(event){
					submitFormTC();
					return false;
				});
			});
      
      $(document).ready(function(){
        $('#plotConfig').submit(function(){
          
          submitPlotConfig();
          return false;
        });
      });
      
    </script>
 
		<style>
			.textDiv {
				display: inline-block;
			}
			.border-right {
				border-right: 1px solid #eee;
			}
			.btn-group-vertical {
				margin-bottom: 20px;
			}
      .no-padding {
        padding: 0 !important;
        margin: 0 !important;
      }
      
      .checkbox label:after {
          content: '';
          display: table;
          clear: both;
      }

.checkbox .cr {
  position: relative;
  display: inline-block;
  border: 1px solid #a9a9a9;
  border-radius: .25em;
  width: 1.3em;
  height: 1.3em;
  float: left;
  margin-right: .5em;
}

.checkbox .cr .cr-icon {
  position: absolute;
  font-size: .8em;
  line-height: 0;
  top: 50%;
  left: 15%;
}

.checkbox label input[type="checkbox"] {
  display: none;
}

.checkbox label input[type="checkbox"]+.cr>.cr-icon {
  opacity: 0;
}

.checkbox label input[type="checkbox"]:checked+.cr>.cr-icon {
  opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled+.cr {
  opacity: .5;
}
		</style>

	</head>
	<body onload="formSubUpdate(); refreshDataPageLoad();">
		<div class="container">
			<div class="page-header">
        <div class="row">
          <div class="col-sm-10">
				    <h1>Annealing Monitor</h1>
          </div>
          <div class="col-sm-2">
            <button class="btn btn-default pull-right" data-toggle="modal" data-target="#settingsModal" onclick="fillFormPlotConfig();fillFormConfig();formSubUpdateT();formSubUpdateP();fillFormSampleRate(); fillFormTC2();">Edit Settings</button>
          </div>
          
        </div>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-md-3 border-right">
        
					<div class="btn-group-vertical">
						<button class="btn btn-default" onclick="window.location.href='plots.html';">Temperature and Pressure Plots</button>
						<button class="btn btn-default" onclick="window.location.href='backupPlots.php';">Download/Plot Legacy Files</button>
                              
          </div>
          
          
					<div class="btn-group-vertical">
						<button class="btn btn-default" onclick="newAnnealingSession()">New Annealing Session</button>
					</div>

				
					<div class="btn-group-vertical">
						<button class="btn btn-default" onclick="restartReadData()">Reboot Data Logger</button>
						<button class="btn btn-default" onclick="stopReadData()">Stop Data Logger</button>
						<!--<button class="btn btn-default" onclick="confirmMessage()">Clear Log Files</button>-->
					</div>

   
         <div id="settingsModal" class="modal" role="dialog">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header">
                      <h1>Settings</h1>
										</div>
										<div class="modal-body">
										  
                        <div class="row">
                        
                          <div class="col-md-2 border-right no-padding">
                          
                            <ul class="nav nav-pills nav-stacked">
                             <li><a class="nav-link" href="#pParam" data-toggle="tab">Plotting Configuration</a></li>
                              <li><a class="nav-link" href="#cTest" data-toggle="tab">Edit Configuration</a></li>
                              <li><a class="nav-link" href="#tConf" data-toggle="tab">Recalibrate Temperature</a></li>
                              <li><a class="nav-link" href="#ionConf" data-toggle="tab">Recalibrate Ion Gauge</a></li>
                              <li><a class="nav-link" href="#tcConf" data-toggle="tab">Recalibrate TC2</a></li>
                              <li><a class="nav-link" href="#pollR" data-toggle="tab">Edit Polling Rate</a></li>
                              <li><a class="nav-link" href="#lFiles" data-toggle="tab" onclick="confirmMessage();">Clear Log Files</a></li>
                            </ul>
                          </div>
                          
                          <div class="col-md-10">
                            <div class="alert alert-success" id="success-alert" style="display: none;">
                              <strong>Success!</strong>  
                            </div>
                            
                            <div class="tab-content" id="settingTabs">
                              <div class="tab-pane" id="pParam">
                                <form id="plotConfig" name="pConfig" role="form">
                                  <div class="form-group">
               											<div class="checkbox">
                                      <label>
                                      <input type="checkbox" id="PT100cBox" class="form-check-input" name="PT100" value="PT100">
                                      <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                      PT100
                                      </label>
                                    </div>
											              <div class="checkbox">
                                      <label>
                                      <input type="checkbox" id="IGcBox" class="form-check-input" name="IG" value="IG">
                                      <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                      IG
                                      </label>
                                    </div>
										                <div class="checkbox">
                                      <label>
                                      <input type="checkbox" id="TC2cBox" class="form-check-input" name="TC2" value="TC2">
                                      <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                      TC2
                                      </label>
                                    </div>
											              <div class="checkbox">
                                      <label>
                                      <input type="checkbox" id="STDevcBox" class="form-check-input" name="STDev" value="STDev">
                                      <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                      STDev
                                      </label>
                                    </div>
                                  </div>
                                  <div class="form-group">
                                    <input type="submit" class="btn btn-primary" id="submit"/>
                                  </div>
                                </form>
                              </div>
                              <div class="tab-pane" id="cTest">
                                <div class="row">
                                <div class="col-xs-2 hidden-xs"></div>
                                <div class="col-xs-6">
                                <div class="well">
                                <form id="configForm" name="config" role="form">
                                
 											            <div class="form-group">                                    
								                    <label for="loc">Current Location:</label>
								                    <input type="text" class="form-control" name="location" id="locInput">
											            </div>
                                                                                                                                                      
											            <div class="form-group">
							                      <label for="mon">Currently Monitoring:</label>
							                      <input type="text" class="form-control" name="monitoring" id="monInput">
					  	                    </div>
                                  <div class="form-group">
                                      <input type="submit" class="btn btn-primary" id="submit">
                                  </div>
                                
                                </form>
                                </div>
                                </div>
                                <div class="col-xs-3"></div>
                                </div>
                              </div>
                              <div class="tab-pane" id="tConf">
                                <div class="row">
                                <div class="col-xs-2 hidden-xs"></div>
                                <div class="col-xs-6">
                                <div class="well">
                                <form id="calibFormT" name="calibT" role="form">
 											            <div class="form-group">
												            <label for="tempConv">TEMP_CONV:</label>
												            <input type="text" class="form-control" name="tempConvConst" id="convInput">
											            </div>
											            <div class="form-group">
												            <label for="tempOff">TEMP_OFFSET:</label>
												            <input type="text" class="form-control" name="tempOffConst" id="offInput">
											            </div>
                                  <div class="form-group">
                                    <input type="submit" class="btn btn-primary" id="submit">
                                  </div>
                                </form>
                                </div>
                                </div>
                                <div class="col-xs-3"></div>
                                </div>
                              </div>
                              <div class="tab-pane" id="ionConf">
                                <div class="row">
                                <div class="col-xs-2 hidden-xs"></div>
                                <div class="col-xs-6">
                                <div class="well">
                                <form id="calibFormP" name="calibP" role="form">
                                   <div class="form-group">
												            <label for="ionConv1">Exp:</label>
												            <input type="text" class="form-control" name="ionConvConst1" id="expInput">
											            </div>
											            <div class="form-group">
												            <label for="ionConv2">Gain:</label>
												            <input type="text" class="form-control" name="ionConvConst2" id="gainInput">
											            </div>
											            <div class="form-group">
												            <label for="ionConv3">VDiff:</label>
												            <input type="text" class="form-control" name="ionConvConst3" id="offsetInput">
											            </div>
                                  <div class="form-group">
                                    <input type="submit" class="btn btn-primary" id="submit">
                                  </div>
                                </form>
                                </div>
                                </div>
                                <div class="col-xs-3"></div>
                                </div>
                              </div>
                              <div class="tab-pane" id="tcConf">
                                <div class="row">
                                <div class="col-xs-2 hidden-xs"></div>
                                <div class="col-xs-6">
                                <div class="well">
                                <form id="tc2Form" name="tcF" role="form">
 											            <div class="form-group">
												            <label for="tcGainInput">Gain:</label>
												            <input type="text" class="form-control" name="tc" id="tcId">
											            </div>
                                  <div class="form-group">
                                    <input type="submit" class="btn btn-primary" id="submit">
                                  </div>
                                </form> 
                                </div>
                                </div>
                                <div class="col-xs-3"></div>
                                </div>
                              </div>

                              <div class="tab-pane" id="lFiles">
                                
                              
                              </div>   
                              <div class="tab-pane" id="pollR">
                                <div class="row">
                                <div class="col-xs-2 hidden-xs"></div>
                                <div class="col-xs-6">
                                <div class="well">
                                <form id="sampleRateForm" name="srf" role="form">
 											            <div class="form-group">
												            <label for="sampleRateInput">Save To File Rate (seconds):</label>
												            <input type="text" class="form-control" name="src" id="sampleRateId">
											            </div>
                                  <div class="form-group">
                                    <input type="submit" class="btn btn-primary" id="submit">
                                  </div>
                                </form>
                                </div>
                                </div>
                                <div class="col-xs-3"></div>
                                </div>
                              </div> 
                              
                                                                     
                            </div>
                          </div>                                                                                    
                        </div>
							
										</div>
										<div class="modal-footer">
	                    <button class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
										
									</div>
								</div>
							</div>

				</div>

				<div class="col-md-7 border-right">
					<b>Current Node:</b> <div id="nodeId" class="textDiv"></div><br>
					<b>Current Location:</b> <div id="locDiv" class="textDiv"></div><br>
					<b>Currently Monitoring:</b> <div id="monDiv" class="textDiv"></div><br>
					<b>Annealing Start Date:</b> <div id="timeDiv" class="textDiv"></div><br> 
					<b>Last Calibrated:</b> <div id="calibDiv" class="textDiv"></div><br> <br>
					<b>Notes:</b> PT100 (1) is the temperature of the detector. IG and TC2 are pressure from the Ion Gauge and Thermocouple 2 (respectively). Ignore PT100 (2), it is not connected. <br> 
					<p><b>The most up-to-date measurement was at...</b> <div id="data"></div></p>
				</div>

				<div class="col-md-2">
					<h4 id="logStatus"></h4>
          <div class="progress" id="rebootBar" style="display:none">
            <div class="progress-bar" id="pBarP" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                <p id="pBarText"></p>
            </div>
          </div>
				</div>
			</div>
		</div>
   
	</body>
</html>

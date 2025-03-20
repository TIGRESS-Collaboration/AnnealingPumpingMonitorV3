<!DOCTYPE html>
<html lang="en">
	<head>
		<title>GRSPi02, Legacy Plots</title>
		<meta charset="utf-8">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  
  <!-- jQuery library -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

  <!-- Popper JS -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

  <!-- Latest compiled JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" integrity="sha512-3q8fi8M0VS+X/3n64Ndpp6Bit7oXSiyCnzmlx6IDBLGlY5euFySyJ46RUlqIVs0DPCGOypqP8IRk/EyPvU28mQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" integrity="sha512-f0VlzJbcEB6KiW8ZVtL+5HWPDyW1+nJEjguZ5IVnSQkvZbwBt2RfCBY0CBO1PsMAqxxrG4Di6TfsCPP3ZRwKpA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

            <script type="text/javascript"
			src="node_modules/dygraphs/dygraph-combined.js"></script>
        <script type="text/javascript"
			src="node_modules/dygraphs/extras/synchronizer.js"></script>
 <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="scripts/backupPlots.js"></script>
   
          <style>
            .textDiv {
                display: inline-block;
            }
            .pull-right {
                line-height: 40px;
            }
            #backButton {
                position: relative;
                top: 30px;
                left: 30px;
            }
            .page-header {
                padding-bottom: 9px;
                margin: 40px 0 20px;
                border-bottom: 1px solid #eee;
            }
            .dropdown-menu {
                white-space: normal;
            
            }
        </style>
        
          
	</head>
 
	<body onload="setRange();">
     <div class="container">
       <div class="page-header">
         <div class="row">
           <div class="col-md-10 col-12">
             <h1>Annealing Monitor</h1>
           </div>
           <div class="col-md-2 col-12">
             <button class="btn btn-light float-right" onclick="window.location.href='index.php';" style="display:inline;">Back</button>
           </div>
         </div>
         <div id="spacer" style="height:1em;"></div>
       </div>
     </div>
        
	  <div class="container">
		  <div class="row">
		    <div class="col-md-3 border-right">
          
            <div class="form-group">
              
                <b><label for="dateRange">Select Date Range to Plot</label></b>
                <input id="idDRT" name="dRT" type="text" class="form-control" autocomplete="off"/>
             
            </div>
            <div class="form-group">
              <button class="btn btn-light" data-toggle="modal" data-target="#plotModal">Plot Selected Range</button>
            </div>
            		<div id="plotModal" class="modal" role="dialog">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title">Choose Data to Plot</h4>
										</div>
										<form id="plotForm" name="plot" role="form">
										<div class="modal-body">
											<div class="form-check">
                        <label class="form-check-label">
                          <input type="checkbox" class="form-check-input" name="plot" value="PT100"/>PT100
                        </label>
                      </div>
											<div class="form-check">
                        <label class="form-check-label">
                          <input type="checkbox" class="form-check-input" name="plot" value="IG"/>IG
                        </label>
                      </div>
											<div class="form-check">
                        <label class="form-check-label">
                          <input type="checkbox" class="form-check-input" name="plot" value="TC2"/>TC2
                        </label>
                      </div>
											<div class="form-check">
                        <label class="form-check-label">
                          <input type="checkbox" class="form-check-input" name="plot" value="STDev"/>STDev
                        </label>
                      </div>
										</div>
										<div class="modal-footer">
											<input type="submit" class="btn btn-success" id="submit"/>
											<button class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
										</form>
									</div>
								</div>
							</div>
          
            
            <div id="spacer" style="height:2em;"></div>
            
          <!--<div class="dropdown">
              <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select a Detector to Plot</button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" onclick="displaySlider();">Test</a>
            </div>
          </div>-->
          
          <div id="spacer" style="height:1em;"></div>
          <div id="scrollbar" style="display:none;">
          <p><b>Input Date Range to Plot</b></p>
          <div class="form-group">
            <input id="ex2" type="text" class="span2" value="" /*data-slider-min="10"*/ /*data-slider-max="1000"*/ data-slider-step="1" />
          
            <b><div id="dateRangeLow"></div>
            <div id="dateRangeHigh"></div></b>
            
          </div>
          <div class="form-group">
            <button class="btn btn-light" onclick="plotSlider();">Plot</button>
          </div>
          </div>
          
          <div id="spacer" style="height:1em;"></div>
          
		        <form onsubmit="this.action = document.getElementById('file').value">
                    <div class="form-group">
		        		<select id="file" class="btn btn-secondary form-control dropdown-toggle">
        				    <option value="default">Select File to Download</option>
        				    <?php
		        		        foreach(glob(dirname(__FILE__) . '/datalogs/*.csv') as $filename){
                		    			$filename = basename($filename);
                		    			echo "<option value='/datalogs/" . $filename . "'>".$filename."</option>";
                				}
            					?>
        				</select>
                
				          <input type="submit" value="Download" id="downSub" class="btn btn-light" style="display:none;"/>
             </div>
            </form>
            
            <!--
 <div class="dropdown">
  <button class="dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Select A File to Download
  <span class="caret"></span></button>
  <form onsubmit="this.action = document.getElementById('file').value">
  <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
  
  <?php
        foreach(glob(dirname(__FILE__) . '/datalogs/*.csv') as $filename){
		        $filename = basename($filename);
	    			echo "<li role='presentation'> <a role='menuitem' href='#' value='/datalogs/" . $filename . "'>".$filename."</a></li>";
		    }
  ?>

  </ul>
  <input type="submit" value="Download" id="downSub" class="btn btn-light" style="display:none;"/>
  </form>
</div>-->
</div>
        
		    <div class="col-md-9 border-right">
          
          <div id="gdiv" style="width:100%;"></div>
          <div id="spacer" style="height:2em;"></div>
          <div id="gdiv2" style="width:100%;"></div>
          <div id="spacer" style="height:2em;"></div>
          <div id="gdiv3" style="width:100%;"></div>
          <div id="spacer" style="height:2em;"></div>
          <div id="gdiv4" style="width:100%;"></div>
        </div>
		    
	    </div>
    </div>
<script>
      $('#plotForm').submit(function(){
        $('#plotModal').modal('toggle');
        var cArray = new Array();
        
        $('input[name="plot"]:checked').each(function() {
         cArray.push($(this).val());
         });
         
        plotDR(cArray);
        return false;
      });

        $('select').change(function(){
          var selectedVal = $("#file option:selected").val();
          var x = document.getElementById("downSub");
          
          if (selectedVal == 'default') {
            x.style.display = "none";
          } else {
            x.style.display = "block";
          }
        });
      
        //$("#ex2").slider({});
        $("#ex2").on("slide", function(slideEvt) {
          let s1 = "Start Date: ";
          let s2 = "End Date: ";
          
          $("#dateRangeLow").text(s1.concat(sMonth, slideEvt.value[0]));
          $("#dateRangeHigh").text(s2.concat(eMonth, slideEvt.value[1]));
        });
      </script>
    
	</body>
</html>

      
      function submitFormTC(){
		    $.ajax({
					type: "POST",
					url: "../action/tcConfig.php",
					cache: "false",
					data: $('form#tc2Form').serialize(),
          success: function(response){
          
            if(response == 0) {
              $("#success-alert").show();
              setTimeout(function(){
                $("#success-alert").hide();
                }, 1500);
            } else {
              alert("error");
            }
          },
          error: function(){
            alert("error");
          }
				});
      }
      
      function submitPlotConfig(){
			  $.ajax({
					type: "POST",
					url: "../action/plotConfig.php",
					cache: "false",
					data: $('form#plotConfig').serialize(),
          success: function(response){
            if(response == 0) {
              $("#success-alert").show();
              setTimeout(function(){
                $("#success-alert").hide();
                }, 1500);
            } else {
                alert("Error saving to file");
            }
          },
          error: function(){
            alert("Error");
          }
				});
      }

			function getDateTime(){
				var today = new Date();
				var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
				var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
				var dateTime = date+' '+time;
				return dateTime;
			}
      
			/** Executes srJSON.php on sample rate form submission.
			*
			*
			*/
			function submitFormSampleRate(){
				$.ajax({
					type: "POST",
					url: "../action/srJSON.php",
					cache: "false",
					data: $('form#sampleRateForm').serialize(),
					success: function(response){
           $("#success-alert").show();
            setTimeout(function(){
              $("#success-alert").hide();
              }, 1500);
						restartReadData();
					},
					error: function(){
						alert("error");
					}
				});
			}

			/**
			 * Executes tempJSON.php on temperature calibration form submission.
			 *
			 *
			 */
			function submitFormT(){
				$.ajax({
					type: "POST",
					url: "../action/tempJSON.php",
					cache: "false",
					data: $('form#calibFormT').serialize(),
					success: function(response){
            if(response == 0) {    
             $("#success-alert").show();
              setTimeout(function(){
                $("#success-alert").hide();
                }, 1500);
					  } else {
                alert("Error saving to file");       
             }
					},
					error: function(){
						alert("error");
					}
				});
			}

			/**
			 * Executes pressJSON.php on pressure form calibration submission.
			 *
			 */


			function submitFormP(){
				$.ajax({
					type: "POST",
					url: "../action/pressJSON.php",
					cache: "false",
					data: $('form#calibFormP').serialize(),
					success: function(response){
           if(response == 0) {
             $("#success-alert").show();
              setTimeout(function(){
                $("#success-alert").hide();
                }, 1500);
					  } else {
                alert("Error saving to file");       
             }
					},
					error: function(){
						alert("error");
					}
				});
			}


			/**
			 * Updates the fields in temperature calibration form to their
			 * current values.
			 *
			 */


			function formSubUpdateT() {
				$.get("../action/fillFormTemp.php", function(data){
					//alert(JSON.stringify(data));
					const obj = JSON.parse(data);
					document.getElementById("convInput").value = obj.conv;
					document.getElementById("offInput").value = obj.offset;


				});
			}


			/**
			 * Updates the fields in pressure calibration form to their
			 * current values.
			 */


			function formSubUpdateP() {
				$.get("../action/fillFormPress.php", function(data){
					//alert(JSON.stringify(data));
					const obj = JSON.parse(data);
					document.getElementById("expInput").value = obj.expFactor;
					document.getElementById("gainInput").value = obj.gain;
					document.getElementById("offsetInput").value = obj.offset;


				});
			}


			/**
			 * Executes on calibration form submission.
			 */

			function submitForm(){
				$.ajax({
					type: "POST",
					url: "../action/configJSON.php",
					cache: "false",
					data: $('form#configForm').serialize(),
					success: function(response){
           if(response == 0) {
             $("#success-alert").show();
              setTimeout(function(){
                $("#success-alert").hide();
                }, 1500);
           } else {
               alert("error saving to file");
           }
                
						//alert(response);
						formSubUpdate();
					},
					error: function(){
						alert("error");
					}
				});
			}


			/**
			 * Executes testButton.php to backup files.
			 * First displays several "are you sure" messages.
			 *
			 */


			function confirmMessage() {
				let backup = "Do you want to create a backup of datalog.csv? If no click cancel";
				let bupBool = 1;
				if (confirm(backup) == true) {
					bupBool = 1;
				} else {
					bupBool = 0;
				}
				let text = "Datalog file will be deleted, are you sure? If no, click cancel";
				if (confirm(text) == true) {
					let extraConfirm = "Are you SURE you want to delete datalog.csv???";
					if (confirm(extraConfirm) == true) {
						$.ajax({
							url: "../action/testButton.php",
							type: 'POST',
							data: {
								backUpBool: bupBool
							},
							success : function (result) {
								alert(result);
							}
						});
					} else {
						alert("Delete operation cancelled!");
					}
				} else {
					alert("failed");
				}
			}

			/**
			 * Updates location/monitoring/time info and executes updateJSON.php.
			 *
			 */


			function formSubUpdate() {
				$.get("../action/updateJSON.php", function(data){
					//alert(JSON.stringify(data));
					const obj = JSON.parse(data);
					$("#locDiv").html(obj.location);
					$("#monDiv").html(obj.monitoring);
					//$("#timeDiv").html(obj.time);

				});
				$.get("../action/calibTime.php", function(data){
					const obj2 = JSON.parse(data);
					$("#calibDiv").html(obj2.calibTime);
				});
        $.get("../action/getStartDate.php", function(data){
          //alert(data);
          $("#timeDiv").html(data);
        });
			}

			/**
			 * Executes getData.php and updates webpage data to the latest measurments.
			 * Checks logger status and updates the displayed status (running or stopped)
			 * if necessary.
			 *
			 */

			function refreshData(prev) {
				$.get("../action/getData.php", function(data){
					$("#data").html(data);
					var prevData = data;
					//If data is different logger is running, return -1
					if(prevData.localeCompare(prev) != 0) {
						loggerStatus(-1);
					} else {
						loggerStatus(0);

					}
					statusId = window.setTimeout(refreshData, 4000, prevData);
				})
			}

			/**
			 * Updates logger status text and text class.
			 *
			 */


			function loggerStatus(status) {
				if(status == -1) {
					$("#logStatus").text("Logger Running");
					$("#logStatus").addClass("text-success");
					$("#logStatus").removeClass("text-danger");


				} else {
					$("#logStatus").text("Logger Off");
					$("#logStatus").addClass("text-danger");
					$("#logStatus").removeClass("text-success");
				}

			}

			/**
			 * Updates measurment and logger status message on page load.
			 *
			 */


			function refreshDataPageLoad() {
				$.get("../action/getData.php", function(data){
					$("#data").html(data);
					$("#logStatus").text("Determining Logger Status....");
					var domainNameArray = window.location.host.split('.');
					var nodeName = domainNameArray[0];
					$("#nodeId").text(nodeName);
					$("#title").text(nodeName);
          
					statusId = window.setTimeout(refreshData, 4000, data);
				})
			}

			/**
			 * Restarts the logger by executing newScreen.php.
			 * Reloads the page after completion.
			 *
			 */


			function restartReadData() {
				let text = "Are you sure you want to kill and restart the logger? This might take a few seconds.";
				if(confirm(text) == true) {
 			    window.clearTimeout(statusId);
		      $("#logStatus").text("Restarting logger, please wait (do not refresh)...");
					$("#logStatus").removeClass("text-success");
					$("#logStatus").removeClass("text-danger");
					$.ajax({
						url: "action/newScreen.php",
						type: 'POST',
						success : function (result) {
							alert(JSON.stringify(result));
							//$("#logStatus").text("Restarting logger, please wait...");
							location.reload();
						}
					});
				} else {
					alert("restart cancelled");
          				location.reload();
				}
			}

			/**
			 * Stops the logger by executing stopLog.php.
			 * Reloads the page after completion.
			 *
			 */


			function stopReadData() {
				let text = "Are you sure you want to stop the logger? Page will refresh.";
				if(confirm(text) == true) {
					$.ajax({
						url: "../action/stopLog.php",
						type: 'POST',
						success : function (result) {
							alert(result);
							//$("#logStatus").text("Determining Logger Status....");
							location.reload();
						}
					});
				} else {
					alert("restart cancelled");
				}
			}

			/**
			 * Starts a new annealing session by executing newSession.php.
			 *
			 */


			function newAnnealingSession() {
				let text = "Are you sure you want to restart the session? Datalog file will be backed up and deleted.";
      

				if(confirm(text) == true) {
        
        $("#settingsModal").modal();
        fillFormConfig();
        $("a[href='#cTest']").tab('show');
        
					$(document).ready(function(){
						$("#configForm").submit(function(event){
							submitForm();

             $("#settingsModal").modal('toggle');

              window.clearTimeout(statusId);
      		      $("#logStatus").text("Restarting logger, please wait (do not refresh)...");
					      $("#logStatus").removeClass("text-success");
					      $("#logStatus").removeClass("text-danger");
							$.ajax({
								url: "../action/newSession.php",
								type: 'POST',
							success : function (result) {
								alert(result);
								location.reload();
								}
							});
							return false;
						});
          });
					

				} else {
            alert("Cancelled new session");
        
        }
			}


			/**
			 * Updates fields in configuration form.
			 *
			 */

			function fillFormConfig() {
				$.get("../action/fillFormConfig.php", function(data){
					//alert(JSON.stringify(data));
					const obj = JSON.parse(data);
					document.getElementById("locInput").value = obj.location;
					document.getElementById("monInput").value = obj.monitoring;
				});
			}
      
      /**
      *
      *
      */
      function fillFormSampleRate() {
			  $.get("../action/fillFormSample.php", function(data){
					//alert(JSON.stringify(data));
					const obj = JSON.parse(data);
					document.getElementById("sampleRateId").value = obj.sr;
				});
      }
      
      /**
      *
      *
      */
      function fillFormPlotConfig(){
 			  $.get("../action/getPlotParams.php", function(data){
					//alert(JSON.stringify(data));
					const obj = JSON.parse(data);
         
					if(obj.PT100 === "PT100"){
            $("#PT100cBox").prop('checked',true);
          }
          if(obj.IG === "IG"){
            $("#IGcBox").prop('checked',true);
          }
          if(obj.TC2 === "TC2"){
            $("#TC2cBox").prop('checked',true);
          }
          if(obj.STDev === "STDev"){
            $("#STDevcBox").prop('checked',true);
          }
				});
      }
      
      /**
      *
      *
      */
      function fillFormTC2() {
 			  $.get("../action/fillFormTC2.php", function(data){
					//alert(JSON.stringify(data));
					const obj = JSON.parse(data);
					document.getElementById("tcId").value = obj.tc;
				});
      
      }


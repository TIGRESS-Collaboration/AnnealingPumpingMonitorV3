      var sMonth;
      var eMonth;
      var startDateMMDD;
      var endDateMMDD;
      var startDateDD;
      var endDateDD;
      var startMM;
      var endMM
      
      function renderDRT() {
      var date_format = new Date();
      
        $('input[name="dRT"]').daterangepicker({
        
          "startDate": startMM+'/'+startDateDD+'/'+date_format.getFullYear(), "endDate": endMM+'/'+endDateDD+'/'+date_format.getFullYear(),
          
          locale: {
            format: 'YYYY/MM/DD'
            }
        });
      }
      
      function plotSlider() {
          //alert("here");
          var mySlider = $("#ex2").slider();
          var curVal = mySlider.slider('getValue');
          var graphArray = ["PT100", "IG"];
          var date_format = new Date();
          
          curVal[0] = [date_format.getFullYear()+startMM+curVal[0]];
          curVal[1] = [date_format.getFullYear()+endMM+curVal[1]];
          
          alert(curVal);
          
          $.ajax({
            type: "POST",
            url: "../action/plotLegacy.php",
            cache: "false",
            data: {dateArray: curVal,
                   sMonth: sMonth,
                   eMonth: eMonth},
            success: function(response){
              alert(JSON.stringify(response));
              createDyGraphs(graphArray);
            }, 
            error: function(){
              alert("error");
            }
          });
        
        }

      $('#plotForm').submit(function(){
        $('#plotModal').modal('toggle');
        var cArray = new Array();
        
        $('input[name="plot"]:checked').each(function() {
         cArray.push($(this).val());
         });
         
        plotDR(cArray);
        return false;
      });
      
  
      
        //$('input[name="dRT"]').daterangepicker({ format: 'dd/mm/yyy' });
    
      
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
        
      
        
        function setRange() {
  				$.get("../action/parseGraph.php", function(data){
					  //alert(JSON.stringify(data));
					  const obj = JSON.parse(data);
             
            startDateMMDD = obj[0].substring(4, 9);
            endDateMMDD = obj[obj.length - 1].substring(4, 9);
            
            //var sessionLength = endDateMMDD - startDateMMDD + 1;
            
             startDateDD = startDateMMDD.substring(2, 4);
             endDateDD = endDateMMDD.substring(2, 4);
            
             startMM = startDateMMDD.substring(0, 2);
             endMM = endDateMMDD.substring(0, 2);
            
            sMonth = monthName(startMM);
            eMonth = monthName(endMM);
            
            let s1 = "Start Date: ";
            let s2 = "End Date: ";
            
            $("#dateRangeLow").text(s1.concat(sMonth, startDateDD));
            $("#dateRangeHigh").text(s2.concat(eMonth, endDateDD));
            
            var date_format = new Date();
        
            
            //$('input[name="dRT"]').daterangepicker({ format: 'yyyy/mm/dd' });
            $('input[name="dRT"]').daterangepicker({
              maxSpan: {
                "days": 12
              },
              minDate: date_format.getFullYear()+'/'+startMM+'/'+startDateDD,
              maxDate: date_format.getFullYear()+'/'+endMM+'/'+endDateDD,
              startDate: date_format.getFullYear()+'/'+startMM+'/'+startDateDD,
              endDate: date_format.getFullYear()+'/'+startMM+'/'+startDateDD,
              locale: {
                format: 'YYYY/MM/DD'
              }
            });
			    });
        }
        
        function displaySlider() {
            let s1 = "Start Date: ";
            let s2 = "End Date: ";
            
            
            
            $("#dateRangeLow").text(s1.concat(sMonth, startDateDD));
            $("#dateRangeHigh").text(s2.concat(eMonth, endDateDD));
            
            var date_format = new Date();
            
            var date1 = new Date(startMM+'/'+startDateDD+'/'+date_format.getFullYear());
            var date2 = new Date(endMM+'/'+endDateDD+'/'+date_format.getFullYear());
            
            var timeDif = date2.getTime() - date1.getTime();
            
            var dayDif = timeDif/(1000 * 3600 * 24);
            
            //$('input[name="dRT"]').daterangepicker({"startDate": startMM+'/'+startDateDD+'/'+date_format.getFullYear(), "endDate": endMM+'/'+endDateDD+'/'+date_format.getFullYear() });
            
            $("#ex2").slider({
              min: parseFloat(startDateDD),
              max: parseFloat(endDateDD),
              value: [parseFloat(startDateDD), parseFloat(endDateDD)]
              });
              
              var x = document.getElementById("scrollbar");
                if (x.style.display === "none") {
                  x.style.display = "block";
                }
        }
        
        function monthName(mon) {
           return ['January ', 'February ', 'March ', 'April ', 'May ', 'June ', 'July ', 'August ', 'September ', 'October ', 'November ', 'December '][mon - 1];
        }
        
       
        
        function plotDR(cBoxArray) {
        
          var dArray = [$('#idDRT').data('daterangepicker').startDate.format('YYYYMMDD'), $('#idDRT').data('daterangepicker').endDate.format('YYYYMMDD')];
          
          $.ajax({
            type: "POST",
            url: "../action/plotLegacy.php",
            cache: "false",
            data: {dateArray: dArray,
                   sMonth: sMonth,
                   eMonth: eMonth},
            success: function(response){
              //alert(JSON.stringify(response));
              
              createDyGraphs(cBoxArray);
            }, 
            error: function(){
              alert("error");
            }
          });
        
        }
        
        function createDyGraphs(plotElements) {
            var g = [];
        
            
            
          if (plotElements.includes("PT100")) {
            g1 = new Dygraph(
            document.getElementById("gdiv"),
             "../datalogs/plotFile.csv",
                {
                title: 'Temperature (RTD1)',
                ylabel: 'Temperature (C)',
                legend: false,
                visibility: [true,false,false,false,false,false,false,false],
                rollPeriod: 1,
                showRoller: true
                }
            );
            g.push(g1);
            
          } else {
            $('#gdiv').empty();
            
          }
          
          if (plotElements.includes("IG")) {
            g2 = new Dygraph(
            document.getElementById("gdiv2"),
            "../datalogs/plotFile.csv",
                {
                title: 'Pressure (IG)',
                ylabel: 'Pressure (Torr)',
                logscale: true,
                labelsSeparateLines: true,
                visibility: [false,false,false,false,true,false,false,false],
                rollPeriod: 1,
                showRoller: true
                }
            );
            g.push(g2);
          } else {
            $('#gdiv2').empty();
            
          }
          
          if (plotElements.includes("TC2")) {
            g3 = new Dygraph(
            document.getElementById("gdiv3"),
             "../datalogs/plotFile.csv",
                {
                title: 'Pressure (TC2)',
                ylabel: 'Pressure (Torr)',
                logscale: true,
                legend: false,
                visibility: [false,false,false,false,false,false,true,false],
                rollPeriod: 1,
                showRoller: true
                }
            );
            g.push(g3);
          } else {
            $('#gdiv3').empty();
            
          }
          
          if (plotElements.includes("STDev")) {
            g4 = new Dygraph(
            document.getElementById("gdiv4"),
            "../datalogs/STDeviation.csv",
                {
                title: 'Standard Deviation',
                ylabel: 'ADC Reading ( 0 - 1023 )',
                logscale: true,
                labelsSeparateLines: true,
                visibility: [false,true,false,true],
                rollPeriod: 1,
                showRoller: true
                }
            ); 
            g.push(g4);
          } else {
            $('#gdiv4').empty();
            
          }
          
          
        
            var sync = Dygraph.synchronize(g,  {zoom: true, range: false, selection: true});
        }
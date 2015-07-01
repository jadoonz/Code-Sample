$(function() {
	
	function AppViewModel() {
		var self = this;
		self.oTable   = ko.observableArray([]);
		self.parTable = ko.observableArray([]);
		self.mTable   = ko.observableArray([]);
		self.aTable   = ko.observableArray([]);
		self.hideme   = ko.observable(false);
		self.counter  = ko.observable(0); 
		self.totalpages = ko.observableArray([]);
		self.page_index = ko.observable(1);
		//pages = ko.observable(3);
		
		mTableClick = function(){
									
			var iDisplayStart  = $("#iDisplayStart").val();
			var iDisplayLength = $("#iDisplayLength").val();
			
			var searchParam = {
					sEcho:1,
					iDisplayStart:iDisplayStart,
					iDisplayLength:iDisplayLength,
					iSortCol_0:0,
					mDataProp_0:'name',
					sSortDir_0: 'asc',
					limit:  '100',
					
			};
										
			$.ajax({
				  type: "POST",
				  url: "/agent/processed-contacts/1",
				  data:searchParam,
				  dataType: "json",
				  success: [mTableCallback,completedContacts]
			  });		  						 
		 }
		 
		 mTableCallback = function(data){
			 self.mTable.removeAll();

			 $("#iDisplayStart").val(data.iDisplayStart);
			 $("#iDisplayLength").val(data.iDisplayLength);
			
			 $.each(data.aaData, function(index, value) {				 
			 	self.mTable.push(value);
			 });
		}
		
		parTableClick = function(){
									
			var iDisplayStart  = $("#iDisplayStart").val();
			var iDisplayLength = $("#iDisplayLength").val();
			
			var searchParam = {
					sEcho:1,
					iDisplayStart:iDisplayStart,
					iDisplayLength:iDisplayLength,
					iSortCol_0:0,
					mDataProp_0:'name',
					sSortDir_0: 'asc',
					limit:  '100',
					
			};
										
			$.ajax({
				  type: "POST",
				  url: "/agent/unprocessed-contacts/4",
				  data:searchParam,
				  dataType: "json",
				  success: [parTableCallback,partiallyContacts]
			  });		  						 
		 }
		 
		 parTableCallback = function(data){
			 self.parTable.removeAll();

			 $("#iDisplayStart").val(data.iDisplayStart);
			 $("#iDisplayLength").val(data.iDisplayLength);
			
			 $.each(data.aaData, function(index, value) {				 
			 	self.parTable.push(value);
			 });
		}
		
		oTableClick = function(page){
			console.log('page:');
			console.log(page);
			
			var iDisplayStart  = $("#iDisplayStart").val();
			var iDisplayLength = $("#iDisplayLength").val();
			self.page_index =  page;
			var searchParam = {
					sEcho:1,
					iDisplayStart:iDisplayStart,
					iDisplayLength:iDisplayLength,
					iSortCol_0:0,
					mDataProp_0:'name',
					sSortDir_0: 'ASC',
					limit:  10,
					page: page,
			};
										
			$.ajax({
				  type: "POST",
				  url: "/agent/unprocessed-contacts/0",
				  data:searchParam,
				  dataType: "json",
				  success: [oTableCallback,openContacts]
			  });		  						 
		 }
		 
		 oTableCallback = function(data){
			 self.oTable.removeAll();
			 self.totalpages.removeAll();
			 			 	
			 $("#iDisplayStart").val(data.iDisplayStart);
			 $("#iDisplayLength").val(data.iDisplayLength);
			 console.log('start from:');
			 console.log(data.start_from); 
			 console.log('end at:');
			 console.log(data.end_at); 
			 
			 for(i=1;i<=data.total_pages; i++){
				 self.totalpages.push(i);
			 }
			 //self.totalPages = ko.observable(data.total_pages);
			 
			 $.each(data.aaData, function(index, value) {				 
			 	self.oTable.push(value);
			 });
			 $(".result_waiting").addClass("invisible");
			 $("#"+self.page_index).addClass("active");
		}
		
		/* all */
		aTableClick = function(){
									
			var iDisplayStart  = $("#iDisplayStart").val();
			var iDisplayLength = $("#iDisplayLength").val();
			
			var searchParam = {
					sEcho:1,
					iDisplayStart:iDisplayStart,
					iDisplayLength:iDisplayLength,
					iSortCol_0:0,
					mDataProp_0:'name',
					sSortDir_0: 'DESC',
					limit:  '100',					
			};
										
			$.ajax({
				  type: "POST",
				  url: "/agent/unprocessed-contacts/all",
				  data:searchParam,
				  dataType: "json",
				  success: [aTableCallback,allContacts]
			  });		  						 
		 }
		 
		 aTableCallback = function(data){
			 self.aTable.removeAll();

			 $("#iDisplayStart").val(data.iDisplayStart);
			 $("#iDisplayLength").val(data.iDisplayLength);
			
			 $.each(data.aaData, function(index, value) {				 
			 	self.aTable.push(value);
			 });
			 $(".result_waiting").addClass("invisible");
		}
		/* all */
		function openContacts(json) {        				
			$("#nr-open-contacts").html("("+json.nr+")");
			if(json.code==="0001") window.location = '/admin/signin';
		}
		
		function partiallyContacts(json) {						
			$("#nr-partially-contacts").html("("+json.nr+")");
			if(json.code==="0001") window.location = '/admin/signin';
		}
		
		function completedContacts(json) {						
			$("#nr-completed-contacts").html("("+json.nr+")");
			if(json.code==="0001") window.location = '/admin/signin';
		}
		
		function allContacts(json) {        				
			$("#nr-all-contacts").html("("+json.nr+")");
			if(json.code==="0001") window.location = '/admin/signin';
		}
		
	}
	
	// Activates knockout.js
	ko.applyBindings(new AppViewModel());
	oTableClick(1);
	mTableClick();
	
});
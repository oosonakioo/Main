@extends('layouts.import')

@section('content')
	<form id="uploadDataForm" method="POST">
		{!! csrf_field() !!}
		<input type="hidden" name="json" />
	</form>
	<div class="container">
			<input type="file" id="fileInput" /><br/>
	</div>
	<script type="text/javascript">
	function fixdata(data) {
	  var o = "", l = 0, w = 10240;
	  for(; l<data.byteLength/w; ++l) o+=String.fromCharCode.apply(null,new Uint8Array(data.slice(l*w,l*w+w)));
	  o+=String.fromCharCode.apply(null, new Uint8Array(data.slice(l*w)));
	  return o;
	}

	var rABS = true; // true: readAsBinaryString ; false: readAsArrayBuffer
	function get_header_row(sheet) {
    var headers = [];
    var range = XLSX.utils.decode_range(sheet['!ref']);
    var C, R = range.s.r; /* start in the first row */
    /* walk every column in the range */
    for(C = range.s.c; C <= range.e.c; ++C) {
        var cell = sheet[XLSX.utils.encode_cell({c:C, r:R})] /* find the cell in the first row */

        var hdr = "UNKNOWN " + C; // <-- replace with your desired default
        if(cell && cell.t) hdr = XLSX.utils.format_cell(cell);

        headers.push(hdr);
    }
    return headers;
}
	function handleFile(e) {
	  var files = e.target.files;
	  var i,f;
	  for (i = 0; i != files.length; ++i) {
	    f = files[i];
			var arr = f.name.split('.');
			if(arr[arr.length - 1] != 'xls' && arr[arr.length - 1] != 'xlsx'){
					alert('Please select xls or xlsx file.');
					return false;
			}
	    var reader = new FileReader();
	    var name = f.name;
	    reader.onload = function(e) {
	      var data = e.target.result;

	      var workbook;
	      if(rABS) {
	        /* if binary string, read with type 'binary' */
	        workbook = XLSX.read(data, {type: 'binary'});
	      } else {
	        /* if array buffer, convert to base64 */
	        var arr = fixdata(data);
	        workbook = XLSX.read(btoa(arr), {type: 'base64'});
	      }
				var first_sheet_name = workbook.SheetNames[0];
					/* Get worksheet */
					var worksheet = workbook.Sheets[first_sheet_name];
					var headers = get_header_row(worksheet);
					var masterCols = ["docudate", "shipdate","custcode","custnameeng", "TemplateNo"];
					var detailCols = ["listno", "goodname","goodprice2","rematotalamnt", "gooddiscamnt"];
					var checkHeader = true;
					for(var i = 0; i < masterCols.length; i++)
					{
							if(headers.indexOf(masterCols[i]) == -1)
							{
									checkHeader = false; break;
							}
					}
					if(checkHeader)
						for(var i = 0; i < detailCols.length; i++)
						{
								if(headers.indexOf(detailCols[i]) == -1)
								{
										checkHeader = false; break;
								}
						}
					if(headers.indexOf("docuno") == -1)
					{
							checkHeader = false;
					}
					if(checkHeader) {
							var json = XLSX.utils.sheet_to_json(worksheet);
							var datas = [];
							for(var i = 0; i < json.length; i++)
							{
									if(json[i].listno == "1"){
											var data = {
													docuno: json[i].docuno,
													PaymentMaster: {},
													PaymentDetail: {}
											};
											for(var j = 0; j < masterCols.length; j++)
													data.PaymentMaster[masterCols[j]] = json[i][masterCols[j]];
											for(var j = 0; j < detailCols.length; j++)
													data.PaymentDetail[detailCols[j]] = json[i][detailCols[j]];
											if(data['docuno'])
													data['docuno'] = Number(data['docuno']);
											else data['docuno'] = null;
											if(data.PaymentMaster['custcode'])
													data.PaymentMaster['custcode'] = Number(data.PaymentMaster['custcode']);
											else data.PaymentMaster['custcode'] = null;
											if(data.PaymentMaster['TemplateNo'])
													data.PaymentMaster['TemplateNo'] = Number(data.PaymentMaster['TemplateNo']);
											else data.PaymentMaster['TemplateNo'] = null;
											if(data.PaymentMaster['docudate'])
													data.PaymentMaster['docudate'] = new Date(data.PaymentMaster['docudate']);
											else data.PaymentMaster['docudate'] = null;
											if(data.PaymentMaster['shipdate'])
													data.PaymentMaster['shipdate'] = new Date(data.PaymentMaster['shipdate']);
											else data.PaymentMaster['shipdate'] = null;
											if(data.PaymentDetail['listno'])
													data.PaymentDetail['listno'] = Number(data.PaymentDetail['listno']);
											else data.PaymentDetail['listno'] = null;
											if(data.PaymentDetail['goodprice2'])
													data.PaymentDetail['goodprice2'] = Number(data.PaymentDetail['goodprice2']);
											else data.PaymentDetail['goodprice2'] = null;
											if(data.PaymentDetail['rematotalamnt'])
													data.PaymentDetail['rematotalamnt'] = Number(data.PaymentDetail['rematotalamnt']);
											else data.PaymentDetail['rematotalamnt'] = null;
											if(data.PaymentDetail['gooddiscamnt'])
													data.PaymentDetail['gooddiscamnt'] = Number(data.PaymentDetail['gooddiscamnt']);
											else data.PaymentDetail['gooddiscamnt'] = null;
											datas.push(data);
									}
							}
							$("#uploadDataForm input[name='json']").val(JSON.stringify(datas));
							$.ajax({
							    type: "POST",
							    url: "{{ Helper::url('import/uploadinvoice') }}",
							    data: $("#uploadDataForm").serialize(),
							    success: function (result)
							    {
											if(result.status == "success"){
													alert("Complete");
											}
											else{
													alert(result.msg);
											}
									},
							    error: function(data){
											alert(data.statusText);
									}
							});
					}
					else {
						alert("Invalid file format.");
					}
	    };
	    reader.readAsBinaryString(f);
	  }
	}
	$("#fileInput").change(handleFile);

	</script>
@endsection

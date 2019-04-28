$ = jQuery;

$(function()
{
	mamnageLists();
});

function mamnageLists()
{
	$(document).on("click", ".js-add-list", function()
	{
		var form = $(this).closest(".form");

		var validation = validateForm(form);

		if (Object.keys(validation).length == 0)
		{
			var listName = form.find("[name='list-name']").val();

			// $.ajax(
			// {
			// 	type     : "POST",
			// 	url      : "/ajax.php",
			// 	dataType : "json",
			// 	data     :
			// 	{
			// 		action : "addList",
			// 		request : {list_name : listName}
			// 	}
			// }).done(function(data)
			// {
			// 	if (data)
			// 	{
			// 		var html =
			// 		'<tr>'+
			// 			'<input type="hidden" name="list-id" value="'+parseInt(data)+'" />'+
			// 			'<td><input type="submit" name="view-list" value="View" /></td>'+
			// 			'<td><input type="text" name="list-name" value="'+listName+'" /></td>'+
			// 			'<td><input type="submit" name="update-list" value="Update" /></td>'+
			// 		'</tr>';

			// 		$(".results-table").append(html);

			// 		toastr.success("New List successfully added");

			// 	}
			// 	else
			// 	{
			// 		toastr.error("An error occurred");
			// 	}
			// }).fail(function(data)
			// {
			// 	toastr.error("Could not perform request");
			// 	console.log(data);
			// });
		}
	});
}

function validateForm(form)
{
	var inputs = form.find("input, select, textarea");
	var result = {};

	if (inputs.length > 0)
	{
		inputsToValidate = [];

		$.each(inputs, function()
		{
			
		});
	}

	console.log(inputs);

	return result;
}
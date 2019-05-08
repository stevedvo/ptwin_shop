$ = jQuery;

$(function()
{
	globalFuncs();
	manageLists();
	manageDepts();
});

function globalFuncs()
{
	$(document).on("click", ".js-select-item", function()
	{
		$(this).parent().addClass("selected");
	});

	$(document).on("click", ".js-unselect-item", function()
	{
		$(this).parent().removeClass("selected");
	});
}

function manageLists()
{
	$(document).on("click", ".js-add-list", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();

		var validation = validateForm(form);

		if (Object.keys(validation).length > 0)
		{
			$.each(validation, function(field, errMsg)
			{
				form.find("[name='"+field+"']").addClass("input-error").after("<p class='error-message'>"+errMsg+"</p>");
			});

			toastr.error("There were validation failures");
		}
		else
		{
			var listName = form.find("[name='list-name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : "/ajax.php",
				dataType : "json",
				data     :
				{
					action  : "addList",
					request : {'list_name' : listName}
				}
			}).done(function(data)
			{
				if (data)
				{
					var html = '<p><a href="/edit-list.php?id='+parseInt(data)+'">'+listName+'</a></p>';

					$(".results-container").append(html);
					$(".results-container").find(".no-results").remove();
					form.find(".input-error").removeClass("input-error");
					form.find("[name='list-name']").val("");

					toastr.success("New List successfully added");

				}
				else
				{
					toastr.error("Could not save List");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-add-item-to-list", function()
	{
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var itemID = parseInt(selectedOption.data("item_id"));
		var listID = parseInt(form.find("[name='list-id']").val());

		$.ajax(
		{
			type     : "POST",
			url      : "/ajax.php",
			dataType : "json",
			data     :
			{
				action  : "addItemToList",
				request :
				{
					'item_id' : itemID,
					'list_id' : listID
				}
			}
		}).done(function(data)
		{
			if (data)
			{
				var html = '<p data-item_id="'+itemID+'">'+selectedOption.text()+'<span class="btn btn-danger btn-sm js-select-item">Select</span><span class="btn btn-danger btn-sm js-unselect-item">Unselect</span></p>';

				$(".list-items-container").append(html);
				$(".list-items-container").find(".no-results").remove();
				selectedOption.remove();

				toastr.success("Item successfully added to List");
			}
			else
			{
				toastr.error("Could not add Item to List");
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-move-items-to-list", function()
	{
		var listItemsContainer = $(".list-items-container");
		var selectedItems = listItemsContainer.find(".selected");
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var targetListID = parseInt(selectedOption.data("list_id"));
		var itemIDs = [];

		if (selectedItems.length > 0)
		{
			$.each(selectedItems, function()
			{
				itemIDs.push(parseInt($(this).data("item_id")));
			});

			$.ajax(
			{
				type     : "POST",
				url      : "/ajax.php",
				dataType : "json",
				data     :
				{
					action  : "moveItemsToList",
					request :
					{
						'item_ids' : itemIDs,
						'list_id'  : targetListID
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					var options = "";

					$.each(selectedItems, function()
					{
						options+= '<option data-item_id="'+$(this).data("item_id")+'">'+$(this).data("description")+'</option>'
					});

					$("select.item-selection").append(options);
					selectedItems.remove();

					if (listItemsContainer.find("p").length == 0)
					{
						listItemsContainer.append('<button class="btn btn-danger btn-sm no-results js-remove-list">Remove List</button>');
					}

					toastr.success("Items successfully added to List");
				}
				else
				{
					toastr.error("Could not add Items to List");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-remove-list", function()
	{
		var listID = parseInt($(this).closest(".list-items-container").data("list_id"));

		$.ajax(
		{
			type     : "POST",
			url      : "/ajax.php",
			dataType : "json",
			data     :
			{
				action  : "removeList",
				request : {'list_id' : listID}
			}
		}).done(function(data)
		{
			if (data)
			{
				toastr.success("List successfully removed");
				var timer = setTimeout(function()
				{
					location.href = "/manage-lists.php";
				}, 750);
			}
			else
			{
				toastr.error("Could not remove List");
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});
}

function manageDepts()
{
	$(document).on("click", ".js-add-department", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();

		var validation = validateForm(form);

		if (Object.keys(validation).length > 0)
		{
			$.each(validation, function(field, errMsg)
			{
				form.find("[name='"+field+"']").addClass("input-error").after("<p class='error-message'>"+errMsg+"</p>");
			});

			toastr.error("There were validation failures");
		}
		else
		{
			var departmentName = form.find("[name='department-name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : "/ajax.php",
				dataType : "json",
				data     :
				{
					action  : "addDepartment",
					request : {'dept_name' : departmentName}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						var html = '<p><a href="/departments/edit/'+parseInt(data.result)+'/">'+departmentName+'</a></p>';

						$(".results-container").append(html);
						$(".results-container").find(".no-results").remove();
						form.find(".input-error").removeClass("input-error");
						form.find("[name='department-name']").val("");
					}

					toastr.success("New Department successfully added");
				}
				else
				{
					toastr.error("Could not save Department");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-add-item-to-department", function()
	{
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var itemID = parseInt(selectedOption.data("item_id"));
		var departmentID = parseInt(form.find("[name='department-id']").val());

		$.ajax(
		{
			type     : "POST",
			url      : "/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Departments",
				action     : "addItemToDepartment",
				request    :
				{
					'item_id' : itemID,
					'dept_id' : departmentID
				}
			}
		}).done(function(data)
		{
			if (data)
			{
				var html = '<p data-item_id="'+itemID+'" data-description="'+selectedOption.text()+'">'+selectedOption.text()+'<span class="btn btn-danger btn-sm js-select-item">Select</span><span class="btn btn-danger btn-sm js-unselect-item">Unselect</span></p>';

				$(".department-items-container").append(html);
				$(".department-items-container").find(".no-results").remove();
				selectedOption.remove();

				toastr.success("Item successfully added to Department");
			}
			else
			{
				toastr.error("Could not add Item to Department");
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-remove-items-from-department", function()
	{
		var departmentItemsContainer = $(".department-items-container");
		var departmentID = parseInt(departmentItemsContainer.data("department_id"));
		var selectedItems = departmentItemsContainer.find(".selected");
		var itemIDs = [];

		if (selectedItems.length > 0)
		{
			$.each(selectedItems, function()
			{
				itemIDs.push(parseInt($(this).data("item_id")));
			});

			$.ajax(
			{
				type     : "POST",
				url      : "/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Department",
					action     : "removeItemsFromDepartment",
					request    :
					{
						'item_ids' : itemIDs,
						'dept_id'  : departmentID
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					// var options = "";

					// $.each(selectedItems, function()
					// {
					// 	options+= '<option data-item_id="'+$(this).data("item_id")+'">'+$(this).data("description")+'</option>'
					// });

					// $("select.item-selection").append(options);
					// selectedItems.remove();

					// if (departmentItemsContainer.find("p").length == 0)
					// {
					// 	departmentItemsContainer.append('<button class="btn btn-danger btn-sm no-results js-remove-department">Remove Department</button>');
					// }

					toastr.success("Items successfully removed from Department");
				}
				else
				{
					toastr.error("Could not rmeove Item(s) from Department");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-remove-department", function()
	{
		var departmentID = parseInt($(this).closest(".department-items-container").data("department_id"));

		$.ajax(
		{
			type     : "POST",
			url      : "/ajax.php",
			dataType : "json",
			data     :
			{
				action  : "removeDepartment",
				request : {'department_id' : departmentID}
			}
		}).done(function(data)
		{
			if (data)
			{
				toastr.success("Department successfully removed");
				var timer = setTimeout(function()
				{
					location.href = "/manage-departments.php";
				}, 750);
			}
			else
			{
				toastr.error("Could not remove Department");
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});
}

function validateForm(form)
{
	var inputs = form.find("input, select, textarea");
	var result = {};

	if (inputs.length > 0)
	{
		$.each(inputs, function()
		{
			var $thisInput = $(this);
			var validation = $thisInput.data("validation");

			if (validation != "" && validation != undefined)
			{
				var criteria = validation.split("_");

				if (criteria.length > 0)
				{
					var criterion = [];

					for (var i = 0; i < criteria.length; i++)
					{
						criterion = criteria[i].split(":");

						switch (criterion[0])
						{
							case 'required':
							{
								if ($thisInput.val().length == 0)
								{
									if (result[$thisInput.attr("name")] == undefined)
									{
										result[$thisInput.attr("name")] = "This field is required. ";
									}
									else
									{
										result[$thisInput.attr("name")]+= "This field is required. ";
									}
								}
							}
							break;
							case 'min-length':
							{
								if ($thisInput.val().length < criterion[1])
								{
									if (result[$thisInput.attr("name")] == undefined)
									{
										result[$thisInput.attr("name")] = "Must be "+criterion[1]+" characters or more. ";
									}
									else
									{
										result[$thisInput.attr("name")]+= "Must be "+criterion[1]+" characters or more. ";
									}
								}
							}
							break;
							case 'max-length':
							{
								if ($thisInput.val().length > criterion[1])
								{
									if (result[$thisInput.attr("name")] == undefined)
									{
										result[$thisInput.attr("name")] = "Must be "+criterion[1]+" characters or less. ";
									}
									else
									{
										result[$thisInput.attr("name")]+= "Must be "+criterion[1]+" characters or less. ";
									}
								}
							}
							break;
						}
					}
				}
			}
		});
	}

	return result;
}
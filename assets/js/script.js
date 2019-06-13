$ = jQuery;

$(function()
{
	globalFuncs();
	manageItems();
	manageLists();
	manageDepts();
	manageOrders();
	quickAdd();
	adminFuncs();
});

function globalFuncs()
{
	$(document).on("click", ".js-select-item", function()
	{
		$(this).closest(".row").addClass("selected");
	});

	$(document).on("click", ".js-unselect-item", function()
	{
		$(this).closest(".row").removeClass("selected");
	});
}

function manageItems()
{
	$(document).on("click", ".js-add-item", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();
		form.find(".input-error").removeClass("input-error");

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
			var description = form.find("[name='description']").val();
			var comments = form.find("[name='comments']").val();
			var defaultQty = form.find("[name='default-qty']").val();
			var link = form.find("[name='link']").val();
			var listID = form.find("[name='list-id'] option:selected").val();
			var addToOrder = form.find("[name='add-to-current-order']").prop("checked");

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Items",
					action     : "addItem",
					request    :
					{
						'description'  : description,
						'comments'     : comments,
						'default_qty'  : defaultQty,
						'link'         : link,
						'list_id'      : listID,
						'add_to_order' : addToOrder
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						toastr.success("New Item successfully added");
						var timer = setTimeout(function()
						{
							location.href = constants.SITEURL+"/items/edit/"+data.result+"/";
						}, 750);
					}
					else
					{
						toastr.error("PDOException");
						console.log(data);
					}
				}
				else
				{
					toastr.error("Could not save Item");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-edit-item", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();
		form.find(".input-error").removeClass("input-error");

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
			var itemID = parseInt(form.data("item_id"));
			var description = form.find("[name='description']").val();
			var comments = form.find("[name='comments']").val();
			var defaultQty = parseInt(form.find("[name='default-qty']").val());
			var link = form.find("[name='link']").val();
			var listID = parseInt(form.find("[name='list-id'] option:selected").val());

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Items",
					action     : "editItem",
					request    :
					{
						'item_id'     : itemID,
						'description' : description,
						'comments'    : comments,
						'default_qty' : defaultQty,
						'link'        : link,
						'list_id'     : listID
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						toastr.success("Item successfully updated");
					}
					else
					{
						toastr.error("PDOException");
						console.log(data);
					}
				}
				else
				{
					toastr.error("Could not save Item");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-add-department-to-item", function()
	{
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var departmentID = parseInt(selectedOption.data("dept_id"));
		var itemID = parseInt(form.find("[name='item-id']").val());

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Items",
				action     : "addDepartmentToItem",
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
				if (data.result == true)
				{
					var html = data.partial_view;

					$(".department-items-container").append(html);
					$(".department-items-container").find(".no-results").remove();
					selectedOption.remove();

					toastr.success("Department successfully added to Item");
				}
				else
				{
					if (data.exception != null)
					{
						toastr.error("PDOException");
						console.log(data.exception);
					}
					else
					{
						toastr.error("Unspecified error");
						console.log(data);
					}
				}
			}
			else
			{
				toastr.error("Could not add Department to Item");
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-remove-departments-from-item", function()
	{
		var departmentItemsContainer = $(".department-items-container");
		var itemID = parseInt(departmentItemsContainer.data("item_id"));
		var selectedItems = departmentItemsContainer.find(".selected");
		var deptIDs = [];

		if (selectedItems.length > 0)
		{
			$.each(selectedItems, function()
			{
				deptIDs.push(parseInt($(this).data("dept_id")));
			});

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Items",
					action     : "removeDepartmentsFromItem",
					request    :
					{
						'dept_ids' : deptIDs,
						'item_id'  : itemID
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.result == true)
					{
						$.each(selectedItems, function()
						{
							$(this).remove();
						});

						if (departmentItemsContainer.find(".result-item").length == 0)
						{
							departmentItemsContainer.html('<p class="no-results">Not added to any Departments.</p>');
						}

						toastr.success("Department(s) successfully detached from Item");
					}
					else
					{
						if (data.exception != null)
						{
							toastr.error("PDOException");
							console.log(data.exception);
						}
						else
						{
							toastr.error("Unspecified error");
							console.log(data);
						}
					}
				}
				else
				{
					toastr.error("Could not remove Department(s) from Item");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-set-primary-dept", function()
	{
		var departmentItemsContainer = $(this).closest(".department-items-container");
		var form = $(this).closest(".form");
		var itemID = parseInt(departmentItemsContainer.data("item_id"));
		var deptID = parseInt(form.data("dept_id"));

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Items",
				action     : "setItemPrimaryDepartment",
				request    :
				{
					'item_id' : itemID,
					'dept_id' : deptID
				}
			}
		}).done(function(data)
		{
			if (data)
			{
				if (data.result == true)
				{
					departmentItemsContainer.find(".primary-dept").removeClass("primary-dept");
					form.addClass("primary-dept");

					toastr.success("Primary Department successfully set");
				}
				else
				{
					if (data.exception != null)
					{
						toastr.error("PDOException");
						console.log(data.exception);
					}
					else
					{
						toastr.error("Unspecified error");
						console.log(data);
					}
				}
			}
			else
			{
				toastr.error("Could not set Primary Department");
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-add-item-to-current-order", function()
	{
		var form = $(this).closest(".form");
		var itemID = parseInt(form.data("item_id"));

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Items",
				action     : "addItemToCurrentOrder",
				request    : {'item_id' : itemID}
			}
		}).done(function(data)
		{
			if (data)
			{
				toastr.success("Item successfully added to Order");

				$.each($(".result-item[data-item_id='"+itemID+"']"), function()
				{
					$(this).addClass("selected");
					$(this).find("button.js-remove-item-from-current-order").data("order_item_id", data.id);
				});
			}
			else
			{
				toastr.error("Could not add Item to Order");
				console.log(data);
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-remove-item-from-current-order", function()
	{
		var form = $(this).closest(".form");
		var itemID = parseInt(form.data("item_id"));
		var orderItemID = parseInt($(this).data("order_item_id"));

		if (!isNaN(orderItemID))
		{
			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Items",
					action     : "removeItemFromCurrentOrder",
					request    : {'order_item_id' : orderItemID}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception != null)
					{
						toastr.error("Could not remove Item from Order: PDOException");
						console.log(data.exception);
					}
					else
					{
						if (!data.result)
						{
							toastr.error("Could not remove Item from Order: Unspecified error");
							console.log(data);
						}
						else
						{
							toastr.success("Item successfully removed from Order");

							$(".result-item[data-item_id='"+itemID+"']").removeClass("selected");
						}
					}
				}
				else
				{
					toastr.error("Could not remove Item from Order");
					console.log(data);
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});
}

function manageLists()
{
	$(document).on("click", ".js-add-list", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();
		form.find(".input-error").removeClass("input-error");

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
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Lists",
					action     : "addList",
					request    : {'list_name' : listName}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						var html = '<p><a href="'+constants.SITEURL+'/lists/edit/'+parseInt(data.result)+'/">'+listName+'</a></p>';

						$(".results-container").append(html);
						$(".results-container").find(".no-results").remove();
						form.find(".input-error").removeClass("input-error");
						form.find("[name='list-name']").val("");

						toastr.success("New List successfully added");
					}
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
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Lists",
				action     : "addItemToList",
				request    :
				{
					'item_id' : itemID,
					'list_id' : listID
				}
			}
		}).done(function(data)
		{
			if (data)
			{
				var html = data.partial_view;

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
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Lists",
					action     : "moveItemsToList",
					request    :
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

	$(document).on("click", ".js-update-list", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();
		form.find(".input-error").removeClass("input-error");

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
			var listID = parseInt(form.find("[name='list-id']").val());
			var listName = form.find("[name='list-name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Lists",
					action     : "editList",
					request    :
					{
						'list_id'   : listID,
						'list_name' : listName
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						toastr.success("List successfully updated");
					}
				}
				else
				{
					toastr.error("Could not update List");
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
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Lists",
				action     : "removeList",
				request    : {'list_id' : listID}
			}
		}).done(function(data)
		{
			if (data)
			{
				if (data.exception == null)
				{
					toastr.success("List successfully removed");
					var timer = setTimeout(function()
					{
						location.href = constants.SITEURL+"/lists/";
					}, 750);
				}
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
		form.find(".input-error").removeClass("input-error");

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
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Departments",
					action     : "addDepartment",
					request    : {'dept_name' : departmentName}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						var html = '<p><a href="'+constants.SITEURL+'/departments/edit/'+parseInt(data.result)+'/">'+departmentName+'</a></p>';

						$(".results-container").append(html);
						$(".results-container").find(".no-results").remove();
						form.find(".input-error").removeClass("input-error");
						form.find("[name='department-name']").val("");

						toastr.success("New Department successfully added");
					}
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
			url      : constants.SITEURL+"/ajax.php",
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
				if (data.result == true)
				{
					var html = data.partial_view;

					$(".department-items-container").append(html);
					$(".department-items-container").find(".no-results").remove();
					selectedOption.remove();

					toastr.success("Item successfully added to Department");
				}
				else
				{
					if (data.exception != null)
					{
						toastr.error("PDOException");
						console.log(data.exception);
					}
					else
					{
						toastr.error("Unspecified error");
						console.log(data);
					}
				}
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
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Departments",
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
					if (data.result == true)
					{
						$.each(selectedItems, function()
						{
							$(this).remove();
						});

						if (departmentItemsContainer.find("p").length == 0)
						{
							departmentItemsContainer.html('<p class="no-results">No Items in this Department</p><button class="btn btn-danger btn-sm no-results js-remove-department">Remove Department</button>');
						}

						toastr.success("Item(s) successfully removed from Department");
					}
					else
					{
						if (data.exception != null)
						{
							toastr.error("PDOException");
							console.log(data.exception);
						}
						else
						{
							toastr.error("Unspecified error");
							console.log(data);
						}
					}
				}
				else
				{
					toastr.error("Could not remove Item(s) from Department");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-update-department", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();
		form.find(".input-error").removeClass("input-error");

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
			var deptID = parseInt(form.find("[name='department-id']").val());
			var deptName = form.find("[name='department-name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Departments",
					action     : "editDepartment",
					request    :
					{
						'dept_id'   : deptID,
						'dept_name' : deptName
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						toastr.success("Department successfully updated");
					}
				}
				else
				{
					toastr.error("Could not update Department");
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
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Departments",
				action     : "removeDepartment",
				request    : {'dept_id' : departmentID}
			}
		}).done(function(data)
		{
			if (data)
			{
				if (data.result == true)
				{
					toastr.success("Department successfully removed");
					var timer = setTimeout(function()
					{
						location.href = constants.SITEURL+"/departments/";
					}, 750);
				}
				else
				{
					if (data.exception != null)
					{
						toastr.error("PDOException");
						console.log(data.exception);
					}
					else
					{
						toastr.error("Unspecified error");
						console.log(data);
					}
				}
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

			if (validation != "" && validation != undefined && validation != null && typeof validation == "object")
			{
				$.each(validation, function(key, value)
				{
					switch (key)
					{
						case 'required':
						{
							if ($thisInput.val() == null || $thisInput.val().length == 0)
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
							if ($thisInput.val().length < value)
							{
								if (result[$thisInput.attr("name")] == undefined)
								{
									result[$thisInput.attr("name")] = "Must be "+value+" characters or more. ";
								}
								else
								{
									result[$thisInput.attr("name")]+= "Must be "+value+" characters or more. ";
								}
							}
						}
						break;
						case 'max-length':
						{
							if ($thisInput.val().length > value)
							{
								if (result[$thisInput.attr("name")] == undefined)
								{
									result[$thisInput.attr("name")] = "Must be "+value+" characters or less. ";
								}
								else
								{
									result[$thisInput.attr("name")]+= "Must be "+value+" characters or less. ";
								}
							}
						}
						break;
						case 'min-value':
						{
							if (parseInt($thisInput.val()) < parseInt(value))
							{
								if (result[$thisInput.attr("name")] == undefined)
								{
									result[$thisInput.attr("name")] = "Must be "+value+" or higher. ";
								}
								else
								{
									result[$thisInput.attr("name")]+= "Must be "+value+" or higher. ";
								}
							}
						}
						break;
						case 'datatype':
						{
							switch (value)
							{
								case 'date':
								{
									var dateReg = /^\d{2}([./-])\d{2}\1\d{4}$/;
									var validFormat = $thisInput.val().match(dateReg);
									var validationPassed = false;

									if (validFormat != null)
									{
										var validDate = moment(validFormat[0].substr(6, 4)+"-"+validFormat[0].substr(3, 2)+"-"+validFormat[0].substr(0, 2));

										if (validDate.format() != "Invalid date")
										{
											validationPassed = true;
										}
									}

									if (!validationPassed)
									{
										if (result[$thisInput.attr("name")] == undefined)
										{
											result[$thisInput.attr("name")] = "Date must be in format dd-mm-yyyy and be valid. ";
										}
										else
										{
											result[$thisInput.attr("name")]+= "Date must be in format dd-mm-yyyy and be valid. ";
										}
									}
								}
								break;
							}
						}
						break;
					}
				});
			}
		});
	}

	return result;
}

function quickAdd()
{
	$.ajax(
	{
		type     : "POST",
		url      : constants.SITEURL+"/ajax.php",
		dataType : "json",
		data     :
		{
			controller : "Items",
			action     : "getAllItems",
			request    : {'all_items' : true}
		}
	}).done(function(data)
	{
		if (data)
		{
			if (data.exception != null)
			{
				toastr.error("QuickAdd: PDOException");
				console.log(data.exception);
			}
			else
			{
				if (data.result == null)
				{
					toastr.error("QuickAdd: Unspecified error");
					console.log(data);
				}
				else
				{
					var availableItems = [];

					$.each(data.result, function()
					{
						availableItems.push(this.description);
					});

					$("#quick-add").autocomplete({source : availableItems});
					$("#add-item-to-previous-order").autocomplete({source : availableItems});
				}
			}
		}
		else
		{
			toastr.error("Could not get Items for QuickAdd");
		}
	}).fail(function(data)
	{
		toastr.error("QuickAdd: Could not perform request");
		console.log(data);
	});

	$(document).on("click", ".js-quick-add-item", function()
	{
		var input = $(this).closest(".form").find("[name='item-description']");
		var itemDescription = input.val();

		if (itemDescription != "")
		{
			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Items",
					action     : "quickAddItem",
					request    : {'description' : itemDescription}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.description == itemDescription)
					{
						toastr.error("QuickAdd: Item not found... redirecting.");
						var timer = setTimeout(function()
						{
							location.href = constants.SITEURL+"/items/create/?description="+itemDescription;
						}, 750);
					}

					input.val("");
					toastr.success("Item added to Current Order");

					if ($(".results-container.current-order").length > 0)
					{
						var currentOrder = $(".results-container.current-order");
						currentOrder.find(".no-results").remove();

						if (currentOrder.find(".form[data-order_item_id='"+data.id+"']").length == 0)
						{
							var html =
							'<div class="row form result-item" data-order_item_id="'+data.id+'">'+
								'<div class="col-xs-8 description-container">'+
									'<p><a href="'+constants.SITEURL+'/items/edit/'+data.item_id+'/">'+data.item.description+'</a></p>'+
								'</div>'+
								'<div class="col-xs-4 quantity-container">'+
									'<input type="number" name="quantity" data-validation="required:1_min-value:1" value="'+data.quantity+'" />'+
								'</div>'+
								'<div class="col-xs-4 col-xs-offset-4 update button-container">'+
									'<button class="btn btn-sm btn-primary pull-right js-update-order-item">Update</button>'+
								'</div>'+
								'<div class="col-xs-4 remove button-container">'+
									'<button class="btn btn-sm btn-danger pull-right js-remove-order-item">Remove</button>'+
								'</div>'+
							'</div>';

							currentOrder.append(html);
						}
					}
				}
				else
				{
					toastr.error("QuickAdd: Could not add Item");
					console.log(data);
				}
			}).fail(function(data)
			{
				toastr.error("QuickAdd: Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-quick-edit-item", function()
	{
		var input = $(this).closest(".form").find("[name='item-description']");
		var itemDescription = input.val();

		if (itemDescription != "")
		{
			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Items",
					action     : "quickEditItem",
					request    : {'description' : itemDescription}
				}
			}).done(function(data)
			{
				if (data)
				{
					location.href = constants.SITEURL+"/items/edit/"+data.id+"/";
				}
				else
				{
					toastr.error("QuickAdd: Could not edit Item");
					console.log(data);
				}
			}).fail(function(data)
			{
				toastr.error("QuickAdd: Could not perform request");
				console.log(data);
			});
		}
	});
}

function manageOrders()
{
	$(document).on("click", ".js-update-order-item", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();
		form.find(".input-error").removeClass("input-error");

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
			var orderItemID = parseInt(form.data("order_item_id"));
			var quantity = parseInt(form.find("[name='quantity']").val());

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Orders",
					action     : "updateOrderItem",
					request    :
					{
						'order_item_id' : orderItemID,
						'quantity'      : quantity
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception != null)
					{
						toastr.error("Could not update Order Item: PDOException");
						console.log(data.exception);
					}
					else
					{
						if (!data.result)
						{
							toastr.error("Could not update Order Item: Unspecified error");
							console.log(data);
						}
						else
						{
							toastr.success("Order Item successfully updated");
						}
					}
				}
				else
				{
					toastr.error("Could not update Order Item");
					console.log(data);
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-remove-order-item", function()
	{
		var form = $(this).closest(".form");
		var orderItemID = parseInt(form.data("order_item_id"));

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Orders",
				action     : "removeOrderItem",
				request    : {'order_item_id' : orderItemID}
			}
		}).done(function(data)
		{
			if (data)
			{
				if (data.exception != null)
				{
					toastr.error("Could not remove Order Item: PDOException");
					console.log(data.exception);
				}
				else
				{
					if (!data.result)
					{
						toastr.error("Could not remove Order Item: Unspecified error");
						console.log(data);
					}
					else
					{
						toastr.success("Order Item successfully removed");
						form.remove();

						if ($(".current-order").find(".result-item").length == 0)
						{
							$(".current-order").append('<p class="no-results">No Items added to Order yet</p>');
						}
					}
				}
			}
			else
			{
				toastr.error("Could not remove Order Item");
				console.log(data);
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-clear-current-order", function()
	{
		var form = $(this).closest(".form");
		var orderID = parseInt(form.data("order_id"));

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Orders",
				action     : "removeAllOrderItemsFromOrder",
				request    : {'order_id' : orderID}
			}
		}).done(function(data)
		{
			if (data)
			{
				if (data.exception != null)
				{
					toastr.error("Could not remove Order Items: PDOException");
					console.log(data.exception);
				}
				else
				{
					if (!data.result)
					{
						toastr.error("Could not remove Order Items: Unspecified error");
						console.log(data);
					}
					else
					{
						$(".current-order").empty().append('<p class="no-results">No Items added to Order yet</p>');
						toastr.success("Order Items successfully removed");
					}
				}
			}
			else
			{
				toastr.error("Could not remove Order Items");
				console.log(data);
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-confirm-current-order", function()
	{
		var form = $(this).closest(".form");
		var orderID = parseInt(form.data("order_id"));

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Orders",
				action     : "confirmOrder",
				request    : {'order_id' : orderID}
			}
		}).done(function(data)
		{
			if (data)
			{
				if (data.exception != null)
				{
					toastr.error("Could not confirm Order: PDOException");
					console.log(data.exception);
				}
				else
				{
					if (!data.result)
					{
						toastr.error("Could not confirm Order: Unspecified error");
						console.log(data);
					}
					else
					{
						toastr.success("Order successfully confirmed");
						var timer = setTimeout(function()
						{
							location.href = constants.SITEURL+"/orders/view/"+orderID+"/";
						}, 750);
					}
				}
			}
			else
			{
				toastr.error("Could not confirm Order");
				console.log(data);
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-add-list-to-current-order", function()
	{
		var form = $(this).closest(".form");
		var orderID = parseInt(form.data("order_id"));
		var listID = parseInt(form.find("select[name='add-list-to-current-order'] option:selected").val());

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Orders",
				action     : "addListToOrder",
				request    :
				{
					'list_id'  : listID,
					'order_id' : orderID
				}
			}
		}).done(function(data)
		{
			if (data)
			{
				toastr.success("List successfully added to Order");

				if ($(".results-container.current-order").length > 0)
				{
					var currentOrder = $(".results-container.current-order");
					var html = "";
					currentOrder.find(".no-results").remove();

					$.each(data, function()
					{
						if (currentOrder.find(".form[data-order_item_id='"+this.id+"']").length == 0)
						{
							html+=
							'<div class="row form result-item" data-order_item_id="'+this.id+'">'+
								'<div class="col-xs-8 description-container">'+
									'<p><a href="'+constants.SITEURL+'/items/edit/'+this.item_id+'/">'+this.item.description+'</a></p>'+
								'</div>'+
								'<div class="col-xs-4 quantity-container">'+
									'<input type="number" name="quantity" data-validation="required:1_min-value:1" value="'+this.quantity+'" />'+
								'</div>'+
								'<div class="col-xs-4 col-xs-offset-4 update button-container">'+
									'<button class="btn btn-sm btn-primary pull-right js-update-order-item">Update</button>'+
								'</div>'+
								'<div class="col-xs-4 remove button-container">'+
									'<button class="btn btn-sm btn-danger pull-right js-remove-order-item">Remove</button>'+
								'</div>'+
							'</div>';
						}
					});

					currentOrder.append(html);
				}
			}
			else
			{
				toastr.error("Could not add List to Order");
				console.log(data);
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-update-order", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();
		form.find(".input-error").removeClass("input-error");

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
			var orderID = parseInt(form.data("order_id"));
			var dateOrdered = form.find("[name='date_ordered']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Orders",
					action     : "updateOrder",
					request    :
					{
						'order_id'     : orderID,
						'date_ordered' : dateOrdered
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception != null)
					{
						toastr.error("Could not update Order: PDOException");
						console.log(data.exception);
					}
					else
					{
						if (!data.result)
						{
							toastr.error("Could not update Order: Unspecified error");
							console.log(data);
						}
						else
						{
							toastr.success("Order successfully updated");
						}
					}
				}
				else
				{
					toastr.error("Could not update Order");
					console.log(data);
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-add-item-to-previous-order", function()
	{
		var orderID = parseInt($(this).closest(".form").data("order_id"));
		var input = $(this).closest(".form").find("[name='item-description']");
		var itemDescription = input.val();

		if (itemDescription != "")
		{
			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Orders",
					action     : "addItemToPreviousOrder",
					request    :
					{
						'order_id'    : orderID,
						'description' : itemDescription
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					input.val("");
					toastr.success("Item added to Order");

					if ($(".results-container.previous-order").length > 0)
					{
						var order = $(".results-container.previous-order");
						order.find(".no-results").remove();

						if (order.find(".form[data-order_item_id='"+data.id+"']").length == 0)
						{
							var html =
							'<div class="row form result-item" data-order_item_id="'+data.id+'">'+
								'<div class="col-xs-8 description-container">'+
									'<p><a href="'+constants.SITEURL+'/items/edit/'+data.item_id+'/">'+data.item.description+'</a></p>'+
								'</div>'+
								'<div class="col-xs-4 quantity-container">'+
									'<input type="number" name="quantity" data-validation="required:1_min-value:1" value="'+data.quantity+'" />'+
								'</div>'+
								'<div class="col-xs-4 col-xs-offset-4 update button-container">'+
									'<button class="btn btn-sm btn-primary pull-right js-update-order-item">Update</button>'+
								'</div>'+
								'<div class="col-xs-4 remove button-container">'+
									'<button class="btn btn-sm btn-danger pull-right js-remove-order-item">Remove</button>'+
								'</div>'+
							'</div>';

							order.append(html);
						}
					}
				}
				else
				{
					toastr.error("Could not add Item");
					console.log(data);
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});
}

function adminFuncs()
{
	$(document).on("click", ".js-reset-item-primary-departments", function()
	{
		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Items",
				action     : "resetPrimaryDepartments",
				request    : {'reset' : true}
			}
		}).done(function(data)
		{
			if (data)
			{
				toastr.success("Done");
			}
			else
			{
				toastr.error("Fail");
				console.log(data);
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});
}
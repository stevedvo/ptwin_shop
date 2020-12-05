$ = jQuery;

$(function()
{
	initNavigation();
	initToastr();
	globalFuncs();
	manageItems();
	manageLists();
	manageDepts();
	manageOrders();
	managePackSizes();
	manageLuckyDips();
	manageMeals();
	quickAdd();
	adminFuncs();
	updateRecentConsumptionParameters();
});

function getURLQueryStringAsObject(queryString)
{
	var queryObject = {};

	if (queryString.length > 0)
	{
		var queryArray = [];
		var queryPart = [];

		queryArray = queryString.substr(1).split("&");

		for (var i = 0; i < queryArray.length; i++)
		{
			queryPart = queryArray[i].split("=");
			queryObject[queryPart[0]] = queryPart[1];
		}
	}

	return queryObject;
}

function setURLQueryStringFromObject(queryObject)
{
	var queryString = "?";

	$.each(queryObject, function(key, value)
	{
		queryString+= key+"="+value+"&";
	});

	queryString = queryString.substr(0, queryString.length - 1);

	return queryString;
}

function initNavigation()
{
	$(document).on("click", ".mobile-navigation-container", function()
	{
		$("nav").toggleClass("nav-open");
	});

	$("main").css({"margin-top" : $("header").height()+15+"px"});
}

function initToastr()
{
	toastr.options.closeButton = true;
	toastr.options.progressBar = true;
}

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
			var listID = parseInt(form.find("[name='list-id'] option:selected").val());
			var addToOrder = form.find("[name='add-to-current-order']").prop("checked") ? 1 : 0;
			var packSizeID = parseInt(form.find("[name='packsize_id'] option:selected").val());

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
						'add_to_order' : addToOrder,
						'packsize_id'  : packSizeID
					}
				}
			}).done(function(data)
			{
				if (data.exception != null)
				{
					toastr.error(`Could not add Item: ${data.exception.message}`);
					console.log(data.exception);
				}
				else
				{
					toastr.success("New Item successfully added");

					var timer = setTimeout(function()
					{
						location.href = constants.SITEURL+"/items/edit/"+data.item.id+"/";
					}, 750);
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
			var muteTemp = form.find("[name='mute-temp']").prop("checked") == true ? 1 : 0;
			var mutePerm = form.find("[name='mute-perm']").prop("checked") == true ? 1 : 0;
			var packSizeID = parseInt(form.find("[name='packsize_id'] option:selected").val());

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
						'list_id'     : listID,
						'mute_temp'   : muteTemp,
						'mute_perm'   : mutePerm,
						'packsize_id' : packSizeID
					}
				}
			}).done(function(data)
			{
				if (!data)
				{
					toastr.error("Could not save Item: unknown error");
					console.log(data);

					return false;
				}

				if (data.exception != null)
				{
					toastr.error(`Could not save Item: ${data.exception.message}`);
					console.log(data);

					return false;
				}

				if (!data.result)
				{
					toastr.error("Could not save Item: unknown error");
					console.log(data);

					return false;
				}

				toastr.success("Item successfully updated");

				return true;
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
			if (!data)
			{
				toastr.error("Could not add Department to Item: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not add Department to Item: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			let html = data.partial_view;

			if (html == null)
			{
				toastr.error("Could not add Department to Item: unknown error");
				console.log(data);

				return false;
			}

			$(".department-items-container").append(html);
			$(".department-items-container").find(".no-results").remove();
			selectedOption.remove();

			toastr.success("Department successfully added to Item");

			return true;
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
				if (!data)
				{
					toastr.error("Could not remove Department(s) from Item: unknown error");
					console.log(data);

					return false;
				}

				if (data.exception != null)
				{
					toastr.error(`Could not remove Department(s) from Item: ${data.exception.message}`);
					console.log(data);

					return false;
				}

				if (!data.result)
				{
					toastr.error("Could not remove Department(s) from Item: unknown error");
					console.log(data);

					return false;
				}

				$.each(selectedItems, function()
				{
					$(this).remove();
				});

				if (departmentItemsContainer.find(".result-item").length == 0)
				{
					departmentItemsContainer.html('<p class="no-results">Not added to any Departments.</p>');
				}

				toastr.success("Department(s) successfully detached from Item");

				return true;
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
			if (!data)
			{
				toastr.error("Could not set Primary Department: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not set Primary Department: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (data.result == null)
			{
				toastr.error("Could not set Primary Department: unknown error");
				console.log(data);

				return false;
			}

			departmentItemsContainer.find(".primary-dept").removeClass("primary-dept");
			form.addClass("primary-dept");

			toastr.success("Primary Department successfully set");

			return true;
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
			if (!data)
			{
				toastr.error("Could not add Item to Order: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not add Item to Order: ${data.exception.message}`);
				console.log(data.exception);

				return false;
			}

			if (data.result == null)
			{
				toastr.error("Could not add Item to Order: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("Item successfully added to Order");

			if ($(".result-item[data-item_id='"+data.result.item.id+"']").length > 0)
			{
				$.each($(".result-item[data-item_id='"+data.result.item.id+"']"), function()
				{
					$(this).addClass("selected");
					$(this).find("button.js-remove-item-from-current-order").data("order_item_id", data.result.id);
				});

				return;
			}

			// adding Item from /items/edit/{id}/
			if (form.hasClass("item-current_order-item"))
			{
				form.data("order_item_id", data.result.id);
				form.find(".order-quantity-container").html('<input type="number" min="1" name="quantity" value="'+data.result.quantity+'" data-validation="{&quot;required&quot;:&quot;1&quot;,&quot;min-value&quot;:&quot;1&quot;}" />');
				form.find(".order-buttons-container button").toggleClass("hidden");

				return;
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
						toastr.error(`Could not remove Item from Order: ${data.exception.message}`);
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

	$(document).on("click", ".js-mute-suggestion", function()
	{
		var $this = $(this);
		var form = $this.closest(".form");
		var itemID = parseInt(form.data("item_id"));
		var muteBasis = $this.data("mute_basis");

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Items",
				action     : "updateItemMuteSetting",
				request    :
				{
					'item_id'    : itemID,
					'mute_basis' : muteBasis
				}
			}
		}).done(function(data)
		{
			if (!data)
			{
				toastr.error("Could not mute Item: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not mute Item: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (!data.result)
			{
				toastr.error("Could not mute Item: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("Item suggestion successfully muted");

			if (form.hasClass("fade-on-mute"))
			{
				form.fadeOut(function()
				{
					form.remove();
				});
			}
			else
			{
				form.removeClass("unmuted-"+muteBasis);
				form.addClass("muted-"+muteBasis);
			}

			return true;
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-unmute-suggestion", function()
	{
		var $this = $(this);
		var form = $this.closest(".form");
		var itemID = parseInt(form.data("item_id"));
		var muteBasis = $this.data("mute_basis");

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Items",
				action     : "updateItemMuteSetting",
				request    :
				{
					'item_id'    : itemID,
					'mute_basis' : muteBasis,
					'unmute'     : 1
				}
			}
		}).done(function(data)
		{
			if (!data)
			{
				toastr.error("Could not unmute Item: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not unmute Item: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (!data.result)
			{
				toastr.error("Could not unmute Item: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("Item successfully unmuted");

			form.removeClass("muted-"+muteBasis);
			form.addClass("unmuted-"+muteBasis);

			return true;
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
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
			if (!data)
			{
				toastr.error(`Could not add Item to List: unknown error`);
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not add Item to List: ${data.exception.message}`);
				console.log(data.exception);

				return false;
			}

			let html = data.partial_view;

			if (html == null)
			{
				toastr.error(`Could not add Item to List: unknown error`);
				console.log(data);

				return false;
			}

			$(".list-items-container").append(html);
			$(".list-items-container").find(".no-results").remove();
			selectedOption.remove();

			toastr.success("Item successfully added to List");

			return true;
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
				if (!data)
				{
					toastr.error("Could not add Items to List: unknown error");
					console.log(data);

					return false;
				}

				if (data.exception != null)
				{
					toastr.error(`Could not add Items to List: ${data.exception.message}`);
					console.log(data);

					return false;
				}

				if (!data.result)
				{
					toastr.error("Could not add Items to List: unknown error");
					console.log(data);

					return false;
				}

				let options = "";

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

				return true;
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
				if (!data)
				{
					toastr.error("Could not update List: unknown error");
					console.log(data);

					return false;
				}

				if (data.exception != null)
				{
					toastr.error(`Could not update List: ${data.exception.message}`);
					console.log(data);

					return false;
				}

				if (!data.result)
				{
					toastr.error("Could not update List: unknown error");
					console.log(data);

					return false;
				}

				toastr.success("List successfully updated");

				return true;
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
			if (!data)
			{
				toastr.error("Could not remove List: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not remove List: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (!data.result)
			{
				toastr.error("Could not remove List: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("List successfully removed");

			var timer = setTimeout(function()
			{
				location.href = constants.SITEURL+"/lists/";
			}, 750);

			return true;
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
					request    :
					{
						'dept_name' : departmentName,
						'seq'       : 0
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					var html = data.partial_view;

					$(".results-container").append(html);
					$(".results-container").find(".no-results").remove();
					form.find(".input-error").removeClass("input-error");
					form.find("[name='department-name']").val("");

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
			if (!data)
			{
				toastr.error("Could not add Item to Department: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not add Item to Department: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			let html = data.partial_view;

			if (html == null)
			{
				toastr.error("Could not add Item to Department: unknown error");
				console.log(data);

				return false;
			}

			$(".department-items-container").append(html);
			$(".department-items-container").find(".no-results").remove();
			selectedOption.remove();

			toastr.success("Item successfully added to Department");

			return true;
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
			var deptSeq = parseInt(form.find("[name='seq']").val());

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
						'dept_name' : deptName,
						'seq'       : deptSeq
					}
				}
			}).done(function(data)
			{
				if (!data)
				{
					toastr.error("Could not update Department: unknown error");
					console.log(data);

					return false;
				}

				if (data.exception != null)
				{
					toastr.error(`Could not update Department: ${data.exception.message}`);
					console.log(data);

					return false;
				}

				if (data.result == null)
				{
					toastr.error("Could not update Department: unknown error");
					console.log(data);

					return false;
				}

				toastr.success("Department successfully updated");

				return true;
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
			if (!data)
			{
				toastr.error("Could not remove Department: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not remove Department: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (!data.result)
			{
				toastr.error("Could not remove Department: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("Department successfully removed");

			var timer = setTimeout(function()
			{
				location.href = constants.SITEURL+"/departments/";
			}, 750);

			return true;
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
							if ($thisInput.val() == null || $thisInput.val().trim().length == 0)
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

function managePackSizes()
{
	$(document).on("click", ".js-add-packsize", function()
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
			var name = form.find("[name='packsize_name']").val();
			var shortName = form.find("[name='packsize_short_name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "PackSizes",
					action     : "addPackSize",
					request    :
					{
						'packsize_name'       : name,
						'packsize_short_name' : shortName
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception != null)
					{
						toastr.error("PDOException: "+data.exception.errorInfo[2]);
					}
					else if (data.partial_view != null)
					{
						var html = data.partial_view;

						$(".results-container").append(html);
						$(".results-container").find(".no-results").remove();
						form.find(".input-error").removeClass("input-error");
						form.find("[name='packsize_name']").val("");
						form.find("[name='packsize_short_name']").val("");

						toastr.success("New Pack Size successfully added");
					}
				}
				else
				{
					toastr.error("Could not save Pack Size");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-edit-packsize", function()
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
			var id = parseInt(form.data("packsize_id"));
			var name = form.find("[name='packsize_name']").val();
			var shortName = form.find("[name='packsize_short_name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "PackSizes",
					action     : "editPackSize",
					request    :
					{
						'packsize_id'         : id,
						'packsize_name'       : name,
						'packsize_short_name' : shortName
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception == null)
					{
						toastr.success("PackSize successfully updated");
					}
					else
					{
						toastr.error("PDOException");
						console.log(data);
					}
				}
				else
				{
					toastr.error("Could not save PackSize");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});
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
				toastr.error(`Could not get Items for QuickAdd: ${data.exception.message}`);
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

					var sortedItems = arraySort(availableItems);

					if ($("#add-item-to-previous-order").length > 0)
					{
						$("#add-item-to-previous-order").autocomplete({source : sortedItems});
					}

					$.ajax(
					{
						type     : "POST",
						url      : constants.SITEURL+"/ajax.php",
						dataType : "json",
						data     :
						{
							controller : "LuckyDips",
							action     : "getAllLuckyDips",
							request    : { 'key' : 'value' } // need to pass something!
						}
					}).done(function(data)
					{
						if (!data)
						{
							toastr.error("QuickAdd: Could not retrieve Lucky Dips - unknown error");
							console.log(data);

							return false;
						}

						if (data.exception != null)
						{
							toastr.error(`QuickAdd: Could not retrieve Lucky Dips - ${data.exception.message}`);
							console.log(data);

							return false;
						}

						if (data.result == null)
						{
							toastr.error("QuickAdd: Could not retrieve Lucky Dips - unknown error");
							console.log(data);

							return false;
						}

						$.each(data.result, function()
						{
							availableItems.push("[LuckyDip] "+this.name);
						});

						let sortedItems = arraySort(availableItems);

						$("#quick-add").autocomplete({source : sortedItems});

						return true;
					}).fail(function(data)
					{
						toastr.error("QuickAdd: Could not retrieve Lucky Dips");
						console.log(data);
					});
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
				if (!data)
				{
					toastr.error("QuickAdd: Could not add Item");
					console.log(data);

					return false;
				}

				if (data.description == itemDescription)
				{
					toastr.error("QuickAdd: "+itemDescription+" not found... redirecting.");
					var timer = setTimeout(function()
					{
						location.href = constants.SITEURL+"/items/create/?description="+itemDescription;
					}, 750);

					return;
				}

				if (data.exception != null)
				{
					toastr.error(`QuickAdd - error adding Item: ${data.exception.message}`);
					console.log(data.exception);

					return false;
				}

				if (data.result == null)
				{
					toastr.error(`QuickAdd - error adding Item`);
					console.log(data);

					return false;
				}

				input.val("");
				toastr.success(data.result.item.description+" added to Current Order");

				if ($(".results-container.current-order").length > 0)
				{
					var currentOrder = $(".results-container.current-order");
					currentOrder.find(".no-results").remove();

					if (currentOrder.find(".form[data-order_item_id='"+data.result.id+"']").length == 0)
					{
						var html = data.partial_view;

						currentOrder.append(html);
					}
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
			if (itemDescription.toLowerCase().indexOf("[luckydip]") > -1)
			{
				$.ajax(
				{
					type     : "POST",
					url      : constants.SITEURL+"/ajax.php",
					dataType : "json",
					data     :
					{
						controller : "LuckyDips",
						action     : "getLuckyDipByName",
						request    : {'luckyDip_name' : itemDescription}
					}
				}).done(function(data)
				{
					if (!data)
					{
						toastr.error("QuickEdit: Could not edit Lucky Dip");
						console.log(data);

						return false;
					}

					if (data.exception != null)
					{
						toastr.error(`QuickEdit - Could not edit Lucky Dip: ${data.exception.message}`);
						console.log(data);

						return false;
					}

					if (data.result == null)
					{
						toastr.error("QuickEdit: Could not edit Lucky Dip");
						console.log(data);

						return false;
					}

					location.href = constants.SITEURL+"/luckydips/edit/"+data.result.id+"/";

					return;
				}).fail(function(data)
				{
					toastr.error("QuickEdit: Could not perform request");
					console.log(data);
				});
			}
			else
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
					if (!data)
					{
						toastr.error("QuickEdit: Could not edit Item");
						console.log(data);

						return false;
					}

					if (data.exception != null)
					{
						toastr.error(`QuickEdit - Could not edit Item: ${data.exception.message}`);
						console.log(data);

						return false;
					}

					if (data.result == null)
					{
						toastr.error("QuickEdit: Could not edit Item");
						console.log(data);

						return false;
					}

					location.href = constants.SITEURL+"/items/edit/"+data.result.id+"/";

					return;
				}).fail(function(data)
				{
					toastr.error("QuickEdit: Could not perform request");
					console.log(data);
				});
			}
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
					toastr.error(`Could not remove Order Item: ${data.exception.message}`);
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

						// initiated from /items/edit/{id}/
						if (form.hasClass("item-current_order-item"))
						{
							form.find(".order-quantity-container").empty();
							form.find(".order-buttons-container button").toggleClass("hidden");
							form.data("order_item_id", "");

							return;
						}

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

	$(document).on("click", ".js-check-order-item", function()
	{
		var $this = $(this);
		var form = $this.closest(".form");
		var orderItemID = parseInt(form.data("order_item_id"));
		var check = $this.data("check") == "check" ? 1 : $this.data("check") == "uncheck" ? 0 : null;

		if (check == null)
		{
			toastr.error("Could not update Order Item");
		}
		else
		{
			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Orders",
					action     : "checkOrderItem",
					request    :
					{
						'order_item_id' : orderItemID,
						'checked'       : check
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					if (data.exception != null)
					{
						toastr.error(`Could not update Order Item: ${data.exception.message}`);
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
							form.removeClass("checked unchecked");

							if (check == 1)
							{
								form.addClass("checked");
							}
							else if (check == 0)
							{
								form.addClass("unchecked");
							}

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

	$(document).on("click", ".js-toggle-checked-items-visibility", function()
	{
		var $this = $(this);

		if ($this.hasClass("checked-off"))
		{
			$(".result-item.checked").show();
			$this.removeClass("checked-off").addClass("checked-on").text("Hide Checked Items");
		}
		else if ($this.hasClass("checked-on"))
		{
			$(".result-item.checked").hide();
			$this.removeClass("checked-on").addClass("checked-off").text("Show Checked Items");
		}
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
			if (!data)
			{
				toastr.error("Could not remove Order Items: Unspecified error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not remove Order Items: ${data.exception.message}`);
				console.log(data.exception);

				return false;
			}

			if (data.result == null)
			{
				toastr.error("Could not remove Order Items: Unspecified error");
				console.log(data);

				return false;
			}

			$(".current-order").empty().append('<p class="no-results">No Items added to Order yet</p>');
			toastr.success("Order Items successfully removed");

			return;
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
					toastr.error(`Could not confirm Order: ${data.exception.message}`);
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
			if (!data)
			{
				toastr.error("Could not add List to Order: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not add List to Order: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (data.partial_view == null)
			{
				toastr.error("Could not add List to Order: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("List successfully added to Order");

			if ($(".results-container.current-order").length > 0)
			{
				let currentOrder = $(".results-container.current-order");
				let html = data.partial_view;
				currentOrder.find(".no-results").remove();

				currentOrder.append(html);
			}

			return true;
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
						toastr.error(`Could not update Order: ${data.exception.message}`);
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
					if (data.exception != null)
					{
						toastr.error(`Could not add Item to Order: ${data.exception.message}`);
						console.log(data.exception);

						return false;
					}

					if (data.partial_view == null || data.result == null)
					{
						toastr.error(`Could not add Item to Order: Unspecified error`);
						console.log(data);

						return false;
					}

					input.val("");
					toastr.success("Item added to Order");

					if ($(".results-container.previous-order").length > 0)
					{
						var order = $(".results-container.previous-order");
						order.find(".no-results").remove();

						if (order.find(".form[data-order_item_id='"+data.result.id+"']").length == 0)
						{
							order.append(data.partial_view);
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
			if (!data)
			{
				toastr.error("Could not reset Primary Departments: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not reset Primary Departments: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (!data.result)
			{
				toastr.error("Could not reset Primary Departments: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("Done");

			return true;
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});
}

function arraySort(availableItems)
{
	var lowerCaseArray = [];
	var sortedArray = [];

	$.each(availableItems, function()
	{
		lowerCaseArray.push(this.toLowerCase());
	});

	lowerCaseArray.sort();

	$.each(lowerCaseArray, function()
	{
		sortedArray.push(ucWords(this));
	});

	return sortedArray;
}

function ucWords(str)
{
	var splitStr = str.toLowerCase().split(' ');

	for (var i = 0; i < splitStr.length; i++)
	{
		splitStr[i] = splitStr[i].charAt(0).toUpperCase()+splitStr[i].substring(1);
	}

	return splitStr.join(' ');
}

function updateRecentConsumptionParameters()
{
	$(document).on("click", ".js-update-recent-consumption", function()
	{
		var $this = $(this);
		var $form = $this.closest(".recent-consumption-form");
		var interval = 0;
		var period = "";

		if ($this.data("reset"))
		{
			interval = constants.DEFAULT_CONSUMPTION_INTERVAL;
			period = constants.DEFAULT_CONSUMPTION_PERIOD;
		}
		else
		{
			interval = parseInt($form.find("input[name='consumption_interval']").val());
			period = $form.find("select[name='consumption_period'] option:selected").val();

			if (interval < 1)
			{
				toastr.error("Invalid Interval.");
				return false;
			}

			if (constants.CONSUMPTION_PERIODS.indexOf(period) == -1)
			{
				toastr.error("Invalid Period.");
				return false;
			}
		}

		if ($this.data('ajax'))
		{
			var itemID = parseInt($form.data('item_id'));

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Items",
					action     : "getItemsRecentOrderStatistics",
					request    :
					{
						'item_id'              : itemID,
						'consumption_interval' : interval,
						'consumption_period'   : period
					}
				}
			}).done(function(data)
			{
				if (!data)
				{
					toastr.error("Invalid request.");
					console.log(data);
				}
				else
				{
					if (data.exception != null)
					{
						toastr.error(`Could not get recent Order statistics: ${data.exception.message}`);
						console.log(data.exception);

						return false;
					}

					if (data.result == null)
					{
						toastr.error(`Could not get recent Order statistics`);
						console.log(data);

						return false;
					}

					$("#itemDailyConsumptionRecent").html(data.result.itemDailyConsumptionRecent);
					$("#itemStockNowRecent").html(data.result.itemStockNowRecent);
					$("#itemStockFutureRecent").html(data.result.itemStockFutureRecent);

					$form.find("input[name='consumption_interval']").val(interval);
					$form.find("select[name='consumption_period']").val(period);

					toastr.success("Recent Consumption Updated");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
		else
		{
			var fullPathname = location.origin+location.pathname;
			var queryObject = getURLQueryStringAsObject(location.search);

			if ($this.data("reset"))
			{
				delete queryObject.consumption_interval;
				delete queryObject.consumption_period;
			}
			else
			{
				queryObject["consumption_interval"] = interval;
				queryObject["consumption_period"] = period;
			}

			var newSearch = setURLQueryStringFromObject(queryObject);

			location.href = fullPathname+newSearch;
		}
	});
}

function manageLuckyDips()
{
	$(document).on("click", ".js-add-luckyDip", function()
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
			var luckyDipName = form.find("[name='luckyDip_name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "LuckyDips",
					action     : "addLuckyDip",
					request    : {'luckyDip_name' : luckyDipName}
				}
			}).done(function(data)
			{
				if (!data)
				{
					toastr.error("Could not save Lucky Dip: unknown error");
					console.log(data);

					return false;
				}

				if (data.exception != null)
				{
					toastr.error(`Could not save Lucky Dip: ${data.exception.message}`);
					console.log(data.exception);

					return false;
				}

				if (data.partial_view == null)
				{
					toastr.error("Could not save Lucky Dip: unknown error");
					console.log(data);

					return false;
				}

				var html = data.partial_view;

				$(".results-container").append(html);
				$(".results-container").find(".no-results").remove();
				form.find(".input-error").removeClass("input-error");
				form.find("[name='luckyDip_name']").val("");

				toastr.success("New Lucky Dip successfully added");
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-add-item-to-luckyDip", function()
	{
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var itemID = parseInt(selectedOption.val());
		var luckyDipID = parseInt(form.find("[name='luckyDip_id']").val());

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "LuckyDips",
				action     : "addItemToLuckyDip",
				request    :
				{
					'item_id'     : itemID,
					'luckyDip_id' : luckyDipID
				}
			}
		}).done(function(data)
		{
			if (!data)
			{
				toastr.error("Could not add Item to Lucky Dip: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not add Item to Lucky Dip: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			let html = data.partial_view;

			if (html == null)
			{
				toastr.error("Could not add Item to Lucky Dip: unknown error");
				console.log(data);

				return false;
			}

			$(".luckyDip-items-container").append(html);
			$(".luckyDip-items-container").find(".no-results").remove();
			selectedOption.remove();

			toastr.success("Item successfully added to Lucky Dip");

			return true;
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-remove-item-from-luckyDip", function()
	{
		var $this = $(this);
		var form = $this.closest(".form");
		var luckyDipItemsContainer = $this.closest(".luckyDip-items-container");
		var luckyDipID = parseInt(luckyDipItemsContainer.data("luckydip_id"));
		var itemID = parseInt(form.data("item_id"));

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "LuckyDips",
				action     : "removeItemFromLuckyDip",
				request    :
				{
					'item_id'     : itemID,
					'luckyDip_id' : luckyDipID,
				},
			}
		}).done(function(data)
		{
			if (!data)
			{
				toastr.error("Could not remove Item from Lucky Dip: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not remove Item from Lucky Dip: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (!data.result)
			{
				toastr.error("Could not remove Item from Lucky Dip: unknown error");
				console.log(data);

				return false;
			}

			form.remove();

			if (luckyDipItemsContainer.find(".result-item").length == 0)
			{
				luckyDipItemsContainer.html('<p class="no-results">No Items in this Lucky Dip</p><button class="btn btn-danger btn-sm no-results js-remove-luckyDip">Delete Lucky Dip</button>');
			}

			toastr.success("Item successfully removed from Lucky Dip");

			reloadPartial("LuckyDipItemSelection", "Items", "getAllItemsNotInLuckyDip", { "luckyDip_id" : luckyDipID }, null, null, "POST");

			return true;
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-update-luckyDip", function()
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
			var luckyDipID = parseInt(form.find("[name='luckyDip_id']").val());
			var luckyDipName = form.find("[name='luckyDip_name']").val();
			var luckyDipListID = form.find("[name='luckyDip_list']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "LuckyDips",
					action     : "editLuckyDip",
					request    :
					{
						'luckyDip_id'      : luckyDipID,
						'luckyDip_name'    : luckyDipName,
						'luckyDip_list_id' : luckyDipListID
					}
				}
			}).done(function(data)
			{
				if (!data)
				{
					toastr.error("Could not update Lucky Dip: unknown error");
					console.log(data);

					return false;
				}

				if (data.exception != null)
				{
					toastr.error(`Could not update Lucky Dip: ${data.exception.message}`);
					console.log(data);

					return false;
				}

				if (!data.result)
				{
					toastr.error("Could not update Lucky Dip: unknown error");
					console.log(data);

					return false;
				}

				toastr.success("Lucky Dip successfully updated");

				return true;
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-remove-luckyDip", function()
	{
		var luckyDipID = parseInt($(this).closest(".luckyDip-items-container").data("luckydip_id"));

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "LuckyDips",
				action     : "removeLuckyDip",
				request    : {'luckyDip_id' : luckyDipID}
			}
		}).done(function(data)
		{
			if (!data)
			{
				toastr.error("Could not remove Lucky Dip: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not remove Lucky Dip: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			if (!data.result)
			{
				toastr.error("Could not remove Lucky Dip: unknown error");
				console.log(data);

				return false;
			}

			toastr.success("Lucky Dip successfully removed");

			let timer = setTimeout(function()
			{
				location.href = constants.SITEURL+"/luckydips/";
			}, 750);

			return true;
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});
}

function manageMeals()
{
	$(document).on("click", ".js-add-meal", function()
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
			var mealName = form.find("[name='meal_name']").val().trim();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Meals",
					action     : "addMeal",
					request    : {'meal_name' : mealName}
				}
			}).done(function(data)
			{
				if (data.exception != null)
				{
					toastr.error(`"Could not create Meal: ${data.exception}"`);
				}
				else
				{
					var html = data.partial_view;

					$("#mealsListItems").html(html);
					form.find(".input-error").removeClass("input-error");
					form.find("[name='meal_name']").val("");

					toastr.success("New Meal successfully added");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-update-meal-name", function()
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
			var mealID = parseInt(form.find("[name='meal_id']").val());
			var mealName = form.find("[name='meal_name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : constants.SITEURL+"/ajax.php",
				dataType : "json",
				data     :
				{
					controller : "Meals",
					action     : "editMeal",
					request    :
					{
						'meal_id'   : mealID,
						'meal_name' : mealName
					}
				}
			}).done(function(data)
			{
				if (data.exception != null)
				{
					toastr.error(`"Could not update Meal: ${data.exception}"`);
				}
				else
				{
					var html = data.partial_view;

					$("#mealsListItems").html(html);

					toastr.success("Meal successfully updated");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
		}
	});

	$(document).on("click", ".js-add-item-to-meal", function()
	{
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var itemID = parseInt(selectedOption.val());
		var mealID = parseInt(form.find("[name='meal_id']").val());

		$.ajax(
		{
			type     : "POST",
			url      : constants.SITEURL+"/ajax.php",
			dataType : "json",
			data     :
			{
				controller : "Meals",
				action     : "addItemToMeal",
				request    :
				{
					'item_id' : itemID,
					'meal_id' : mealID
				}
			}
		}).done(function(data)
		{
			if (!data)
			{
				toastr.error("Could not add Item to Meal: unknown error");
				console.log(data);

				return false;
			}

			if (data.exception != null)
			{
				toastr.error(`Could not add Item to Meal: ${data.exception.message}`);
				console.log(data);

				return false;
			}

			let html = data.partial_view;

			if (!html)
			{
				toastr.error("Could not add Item to Meal: unknown error");
				console.log(data);

				return false;
			}

			$(".meal-items-container").append(html);
			$(".meal-items-container").find(".no-results").remove();
			selectedOption.remove();

			toastr.success("Item successfully added to Meal");

			return true;
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	// $(document).on("click", ".js-remove-item-from-meal", function()
	// {
	// 	var $this = $(this);
	// 	var form = $this.closest(".form");
	// 	var mealItemsContainer = $this.closest(".meal-items-container");
	// 	var mealID = parseInt(mealItemsContainer.data("luckydip_id"));
	// 	var itemID = parseInt(form.data("item_id"));

	// 	$.ajax(
	// 	{
	// 		type     : "POST",
	// 		url      : constants.SITEURL+"/ajax.php",
	// 		dataType : "json",
	// 		data     :
	// 		{
	// 			controller : "Meals",
	// 			action     : "removeItemFromMeal",
	// 			request    :
	// 			{
	// 				'item_id'     : itemID,
	// 				'meal_id' : mealID
	// 			}
	// 		}
	// 	}).done(function(data)
	// 	{
	// 		if (data)
	// 		{
	// 			if (data.result == true)
	// 			{
	// 				form.remove();

	// 				if (mealItemsContainer.find(".result-item").length == 0)
	// 				{
	// 					mealItemsContainer.html('<p class="no-results">No Items in this Lucky Dip</p><button class="btn btn-danger btn-sm no-results js-remove-meal">Delete Lucky Dip</button>');
	// 				}

	// 				toastr.success("Item successfully removed from Lucky Dip");

	// 				reloadPartial("MealItemSelection", "Items", "getAllItemsNotInMeal", { "meal_id" : mealID }, null, null, "POST");
	// 			}
	// 			else
	// 			{
	// 				if (data.exception != null)
	// 				{
	// 					toastr.error("PDOException");
	// 					console.log(data.exception);
	// 				}
	// 				else
	// 				{
	// 					toastr.error("Unspecified error");
	// 					console.log(data);
	// 				}
	// 			}
	// 		}
	// 		else
	// 		{
	// 			toastr.error("Could not remove Item from Lucky Dip");
	// 		}
	// 	}).fail(function(data)
	// 	{
	// 		toastr.error("Could not perform request");
	// 		console.log(data);
	// 	});
	// });

	// $(document).on("click", ".js-remove-meal", function()
	// {
	// 	var mealID = parseInt($(this).closest(".meal-items-container").data("luckydip_id"));

	// 	$.ajax(
	// 	{
	// 		type     : "POST",
	// 		url      : constants.SITEURL+"/ajax.php",
	// 		dataType : "json",
	// 		data     :
	// 		{
	// 			controller : "Meals",
	// 			action     : "removeMeal",
	// 			request    : {'meal_id' : mealID}
	// 		}
	// 	}).done(function(data)
	// 	{
	// 		if (data)
	// 		{
	// 			if (data.result == true)
	// 			{
	// 				toastr.success("Lucky Dip successfully removed");
	// 				var timer = setTimeout(function()
	// 				{
	// 					location.href = constants.SITEURL+"/luckydips/";
	// 				}, 750);
	// 			}
	// 			else
	// 			{
	// 				if (data.exception != null)
	// 				{
	// 					toastr.error("PDOException");
	// 					console.log(data.exception);
	// 				}
	// 				else
	// 				{
	// 					toastr.error("Unspecified error");
	// 					console.log(data);
	// 				}
	// 			}
	// 		}
	// 		else
	// 		{
	// 			toastr.error("Could not remove Lucky Dip");
	// 		}
	// 	}).fail(function(data)
	// 	{
	// 		toastr.error("Could not perform request");
	// 		console.log(data);
	// 	});
	// });
}

function reloadPartial(id, controller, action, params, callback, onbeforeload, ajaxMethod)
{
	ajaxMethod = ajaxMethod || "GET";

	let queryString = buildQueryString(params);
	id = id.startsWith('#') ? id : "#" + id;

	/// Before you load do this.
	if (typeof (onbeforeload) == "function")
	{
		onbeforeload();
	}

	let target = $(id);

	$.ajax(
	{
		url      : constants.SITEURL+"/ajax.php",
		type     : ajaxMethod.toUpperCase(),
		dataType : "json",
		data     :
		{
			controller : controller,
			action     : action,
			request    : params,
		},
		success : function(response)
		{
			if (!response)
			{
				toastr.error("Could not reload Partial: unknown error");
				console.log(response);

				return false;
			}

			if (response.exception != null)
			{
				toastr.error(`Could not reload Partial: ${response.exception.message}`);
				console.log(response);

				return false;
			}

			let html = data.partial_view;

			if (html == null)
			{
				toastr.error("Could not reload Partial: unknown error");
				console.log(data);

				return false;
			}

			target.html(html);
			reloadSuccessFunction(id, params, callback);
		},
	});
}

function buildQueryString(params)
{
	let queryString = "";
	let count = 0;

	if (params)
	{
		Object.keys(params).forEach(function(key)
		{
			if (count == 0)
			{
				queryString+= key+"="+params[key];
			}
			else
			{
				queryString+= "&"+key+"="+params[key];
			}

			count++;
		});
	}

	return queryString;
}

function reloadSuccessFunction(id, params, callback)
{
	id = id.startsWith('#') ? id : "#" + id;

	/// Once you have loaded do this.
	if (typeof (callback) == "function")
	{
		callback();
	}
}

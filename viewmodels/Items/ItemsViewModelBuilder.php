<?php
	declare(strict_types=1);

	class ItemsViewModelBuilder
	{
		public function __construct() { }

		public function createSuggestionsViewModels(array $suggestedItems, Order $currentOrder) : array
		{
			$suggestionsViewModels = [];

			foreach ($suggestedItems as $itemId => $item)
			{
				$inCurrentOrder = false;
				$orderItemId = null;
				$totalMealItemsQuantity = 0;

				if ($item->hasUpcomingMealItems())
				{
					foreach ($item->getUpcomingMealItems() as $dateString => $mealItem)
					{
						$totalMealItemsQuantity+= $mealItem->getQuantity();
					}
				}

				$orderItem = $currentOrder->getOrderItemByItemId($item->getId());

				if ($orderItem instanceof OrderItem)
				{
					$inCurrentOrder = true;
					$orderItemId = $orderItem->getId();
					$suggestedItemQuantity = $orderItem->getQuantity();
				}
				else
				{
					$suggestedItemQuantity = $item->getDefaultQty();
				}

				$suggestedItemQuantity = max($suggestedItemQuantity, $totalMealItemsQuantity);

				$suggestionsViewModel = new SuggestionsViewModel($item->getId(), $item->getDescription(), $suggestedItemQuantity, $inCurrentOrder, $orderItemId);
				$suggestionsViewModels[$suggestionsViewModel->getId()] = $suggestionsViewModel;
			}

			return $suggestionsViewModels;
		}
	}

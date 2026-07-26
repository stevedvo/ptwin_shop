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
				$suggestionsViewModel = $this->createSuggestionViewModel($item, $currentOrder);
				$suggestionsViewModels[$suggestionsViewModel->getId()] = $suggestionsViewModel;
			}

			return $suggestionsViewModels;
		}

		public function createMealItemSuggestionsViewModels(array $suggestedItems, Order $currentOrder) : array
		{
			$suggestionsViewModels = [];

			foreach ($suggestedItems as $itemId => $item)
			{
				$totalMealItemsQuantity = 0;

				foreach ($item->getMealItems() as $dateString => $mealItem)
				{
					$totalMealItemsQuantity+= $mealItem->getQuantity();
				}

				$suggestionsViewModel = $this->createSuggestionViewModel($item, $currentOrder, $totalMealItemsQuantity);
				$suggestionsViewModels[$suggestionsViewModel->getId()] = $suggestionsViewModel;
			}

			return $suggestionsViewModels;
		}

		private function createSuggestionViewModel(Item $item, Order $currentOrder, ?int $minimumQuantity = null) : SuggestionsViewModel
		{
			$inCurrentOrder = false;
			$orderItemId = null;

			$orderItem = $currentOrder->getOrderItemByItemId($item->getId());

			if ($orderItem instanceof OrderItem)
			{
				$inCurrentOrder = true;
				$orderItemId = $orderItem->getId();
				$suggestedItemQuantity = intval($orderItem->getQuantity());
			}
			else
			{
				$suggestedItemQuantity = intval($item->getDefaultQty());
			}

			if (!is_null($minimumQuantity))
			{
				$suggestedItemQuantity = max($suggestedItemQuantity, $minimumQuantity);
			}

			return new SuggestionsViewModel($item->getId(), $item->getDescription(), $suggestedItemQuantity, $inCurrentOrder, $orderItemId);
		}
	}

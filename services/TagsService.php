<?php
	declare(strict_types=1);

	class TagsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new TagsDAL();
		}

		public function closeConnexion() : void
		{
			$this->dal->closeConnexion();
		}

		public function verifyTagRequest(array $request) : Tag
		{
			try
			{
				if (!is_numeric($request['tag_id']))
				{
					throw new Exception("Invalid Tag ID");
				}

				$tag = $this->getTagById(intval($request['tag_id']));

				return $tag;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function verifyTagItemRequest(array $request) : TagItem
		{
			try
			{
				if (!is_numeric($request['tag_item_id']))
				{
					throw new Exception("Invalid Tag Item ID");
				}

				$tagItem = $this->getTagItemById(intval($request['tag_item_id']));

				return $tagItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function tagNameExists(string $tagName, bool $includeDeleted = false) : bool
		{
			try
			{
				$tag = $this->dal->getTagByName($tagName, $includeDeleted);

				return ($tag instanceof Tag);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function tagNameIsUnique(string $tagName, int $tagId, bool $includeDeleted = false) : bool
		{
			try
			{
				$tag = $this->dal->getTagByName($tagName, $includeDeleted);

				if (!$tag instanceof Tag)
				{
					return true;
				}

				return ($tag->getId() == $tagId);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addTag(Tag $tag) : Tag
		{
			try
			{
				if ($this->tagNameExists($tag->getName()))
				{
					throw new Exception("Tag Name '".$tag->getName()."' already exists");
				}

				return $this->dal->addTag($tag);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllTags() : array
		{
			try
			{
				$tags = $this->dal->getAllTags();

				if (!is_array($tags))
				{
					throw new Exception("Tags not found");
				}

				return $tags;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getTagById(int $tagId) : Tag
		{
			try
			{
				$tag = $this->dal->getTagById($tagId);

				if (!($tag instanceof Tag))
				{
					throw new Exception("Tag not found");
				}

				return $tag;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function updateTag(Tag $tagUpdate) : Tag
		{
			try
			{
				$tag = $this->getTagById($tagUpdate->getId());

				if (is_null($tag))
				{
					throw new Exception("Cannot find Tag with ID '".$tagUpdate->getId()."'");
				}

				if (!$this->tagNameIsUnique($tagUpdate->getName(), $tag->getId()))
				{
					throw new Exception("A Tag with Name '".$tagUpdate->getName()."' already exists");
				}

				$tag->setName($tagUpdate->getName());

				return $this->dal->updateTag($tag);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addItemToTag(Tag $tag, Item $item) : TagItem
		{
			try
			{
				$tagItem = createTagItem(
				[
					'tag_id'            => $tag->getId(),
					'item_id'            => $item->getId(),
					'tag_item_quantity' => $item->getDefaultQty(),
				]);

				if (!entityIsValid($tagItem))
				{
					throw new Exception("Invalid TagItem");
				}

				$tagItem->setTag($tag);
				$tagItem->setItem($item);

				$tagItem = $this->addTagItem($tagItem);

				return $tagItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addTagItem(TagItem $tagItem) : TagItem
		{
			try
			{
				$tagItem = $this->dal->addTagItem($tagItem);

				if (!($tagItem instanceof TagItem))
				{
					throw new Exception("TagItem could not be added to DB");
				}

				return $tagItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getTagItemById(int $tagItemId) : TagItem
		{
			try
			{
				$tagItem = $this->dal->getTagItemById($tagItemId);

				if (!($tagItem instanceof TagItem))
				{
					throw new Exception("Tag Item not found");
				}

				return $tagItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function updateTagItem(TagItem $tagItem) : bool
		{
			try
			{
				if (!entityIsValid($tagItem))
				{
					throw new Exception("Invalid Tag Item quantity");
				}

				return $this->dal->updateTagItem($tagItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeItemFromTag(TagItem $tagItem, Tag $tag) : bool
		{
			try
			{
				if ($tagItem->getTagId() != $tag->getId())
				{
					throw new Exception("Tag ID mismatch");
				}

				return $this->removeTagItem($tagItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeTagItem(TagItem $tagItem) : bool
		{
			try
			{
				return $this->dal->removeTagItem($tagItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeTag(Tag $tag) : bool
		{
			try
			{
				$tag->setIsDeleted(true);

				return $this->dal->removeTag($tag);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function restoreTag(Tag $tag) : Tag
		{
			try
			{
				$tag->setIsDeleted(false);

				return $this->dal->restoreTag($tag);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getTagPlansInDateRange(DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo) : array
		{
			try
			{
				$tagPlans = $this->dal->getTagPlansInDateRange($dateFrom, $dateTo);

				if (!is_array($tagPlans))
				{
					throw new Exception("Tag Plans not found");
				}

				return $tagPlans;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getTagPlanByDate(array $request) : TagPlanDay
		{
			try
			{
				if (!isset($request['dateString']))
				{
					throw new Exception("Date not provided");
				}

				$date = sanitiseDate($request['dateString']);

				if (!($date instanceof DateTime))
				{
					throw new Exception("Invalid date");
				}

				$tagPlan = $this->dal->getTagPlanByDate($date);

				return $tagPlan;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function updateTagPlanDay(array $request) : TagPlanDay
		{
			$dalResult = new DalResult();

			try
			{
				$tagPlanDayUpdate = createTagPlanDay($request);

				if (!($tagPlanDayUpdate->getDate() instanceof DateTimeInterface))
				{
					throw new Exception("Invalid date");
				}

				if (!is_null($tagPlanDayUpdate->getTagId()))
				{
					$tag = $this->getTagById($tagPlanDayUpdate->getTagId());
					$tagPlanDayUpdate->setTag($tag);
				}

				// is status valid?

				$tagPlanDay = $this->dal->getTagPlanByDate($tagPlanDayUpdate->getDate());

				if (is_null($tagPlanDay->getId()))
				{
					$tagPlanDay = $this->dal->addTagPlanDay($tagPlanDayUpdate);
				}
				else
				{
					if ($tagPlanDay->getTagId() != $tagPlanDayUpdate->getTagId())
					{
						$tagPlanDay->setTagId($tagPlanDayUpdate->getTagId());
						$tagPlanDay->setTag($tagPlanDayUpdate->getTag());
						$tagPlanDay->setOrderItemStatus($tagPlanDayUpdate->getOrderItemStatus());

						$tagPlanDay = $this->dal->updateTagPlanDay($tagPlanDay);
					}
				}

				$itemsToUpdate = [];

				foreach ($tagPlanDayUpdate->getTagItems() as $key => $tagItem)
				{
					if (!is_null($tagItem->getItemId()) && !in_array($tagItem->getItemId(), $itemsToUpdate))
					{
						$itemsToUpdate[] = $tagItem->getItemId();
					}
				}

				if (count($itemsToUpdate) > 0)
				{
					$success = $this->items_service->updateTagPlanChecks($itemsToUpdate);

					if (!$success)
					{
						throw new Exception("Tag Plan day updated but failed to set Items to check");
					}
				}

				return $tagPlanDay;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}

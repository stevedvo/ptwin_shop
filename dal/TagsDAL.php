<?php
	declare(strict_types=1);

	class TagsDAL
	{
		private $ShopDb;

		public function __construct()
		{
			$this->ShopDb = new ShopDb();
		}

		public function closeConnexion() : void
		{
			$this->ShopDb = null;
		}

		public function addTag(Tag $tag) : Tag
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO tags (name) VALUES (:name)");
				$query->execute([':name' => $tag->getName()]);

				$tag->setId(intval($this->ShopDb->conn->lastInsertId()));
			}
			catch(PDOException $e)
			{
				throw $e;
			}

			return $tag;
		}

		public function getTagById(int $tagId) : ?Tag
		{
			try
			{
				$tag = null;

				$query = $this->ShopDb->conn->prepare("SELECT m.id AS tag_id, m.name AS tag_name, m.IsDeleted AS tag_isDeleted, mi.id AS tag_item_id, mi.quantity AS tag_item_quantity, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.tag_plan_check FROM tags AS m LEFT JOIN tag_items AS mi ON (mi.tag_id = m.id) LEFT JOIN items AS i ON (i.item_id = mi.item_id) WHERE m.id = :id ORDER BY i.description");
				$query->execute([':id' => $tagId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					foreach ($rows as $row)
					{
						if (!($tag instanceof Tag))
						{
							$tag = createTag($row);
						}

						if (!is_null($row['tag_item_id']))
						{
							$item = createItem($row);

							if (!entityIsValid($item))
							{
								throw new Exception("Invalid Item. Item ID #".$item->getId());
							}

							$tagItem = createTagItem($row);
							$tagItem->setItem($item);

							if (!entityIsValid($tagItem))
							{
								throw new Exception("Invalid TagItem. TagItem ID #".$tagItem->getId());
							}

							$tag->addTagItem($tagItem);
						}
					}
				}

				return $tag;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getTagByName(string $tagName, bool $includeDeleted) : ?Tag
		{
			$tag = null;

			$includeDeletedQuery = $includeDeleted ? "" : "AND IsDeleted = 0";

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS tag_id, name AS tag_name, IsDeleted AS tag_isDeleted FROM tags WHERE name = :name ".$includeDeletedQuery." LIMIT 1");
				$query->execute([':name' => $tagName]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$tag = createTag($row);
				}
			}
			catch(PDOException $e)
			{
				throw $e;
			}

			return $tag;
		}

		public function getAllTags() : ?array
		{
			try
			{
				$tags = null;

				$query = $this->ShopDb->conn->prepare("SELECT t.id AS tag_id, t.name AS tag_name, m.id AS meal_id, m.name AS meal_name FROM tags AS t LEFT JOIN meals_tags AS mt ON (mt.tag_id = t.id) LEFT JOIN meals AS m ON (m.id = mt.meal_id) WHERE m.IsDeleted = 0 ORDER BY t.name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$tags = [];

					foreach ($rows as $row)
					{
						if (!isset($tags[$row['tag_id']]))
						{
							$tag = createTag($row);

							$tags[$tag->getId()] = $tag;
						}

						if (!is_null($row['meal_id']))
						{
							$meal = createMeal($row);
							$tags[$row['tag_id']]->addMeal($meal);
						}
					}
				}

				return $tags;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function updateTag(Tag $tag) : Tag
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE tags SET name = :name WHERE id = :id");
				$query->execute(
				[
					':name' => $tag->getName(),
					':id'   => $tag->getId(),
				]);

				return $tag;
			}
			catch(PDOException $e)
			{
				throw $e;
			}
		}

		public function addTagItem(TagItem $tagItem) : TagItem
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO tag_items (tag_id, item_id, quantity) VALUES (:tag_id, :item_id, :quantity)");
				$query->execute(
				[
					':tag_id'  => $tagItem->getTagId(),
					':item_id'  => $tagItem->getItemId(),
					':quantity' => $tagItem->getQuantity(),
				]);

				$tagItem->setId(intval($this->ShopDb->conn->lastInsertId()));

				return $tagItem;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getTagItemById(int $tagItemId) : ?TagItem
		{
			try
			{
				$tagItem = null;

				$query = $this->ShopDb->conn->prepare("SELECT mi.id AS tag_item_id, mi.tag_id, mi.item_id, mi.quantity AS tag_item_quantity, m.name AS tag_name, m.IsDeleted AS tag_isDeleted, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.tag_plan_check FROM tag_items AS mi LEFT JOIN tags AS m ON (m.id = mi.tag_id) LEFT JOIN items AS i ON (i.item_id = mi.item_id) WHERE mi.id = :id");
				$query->execute([':id' => $tagItemId]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if (is_array($row))
				{
					$tagItem = createTagItem($row);

					if (!entityIsValid($tagItem))
					{
						throw new Exception("Invalid Tag Item. #".$tagItem->getId());
					}

					$tag = createTag($row);

					if (!entityIsValid($tag))
					{
						throw new Exception("Invalid Tag. Tag ID #".$tag->getId());
					}

					$tagItem->setTag($tag);

					$item = createItem($row);

					if (!entityIsValid($item))
					{
						throw new Exception("Invalid Item. Item ID #".$item->getId());
					}

					$tagItem->setItem($item);
				}

				return $tagItem;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function updateTagItem(TagItem $tagItem) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE tag_items SET quantity = :quantity WHERE id = :id");
				$success = $query->execute(
				[
					':quantity' => $tagItem->getQuantity(),
					':id'       => $tagItem->getId(),
				]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function removeTagItem(TagItem $tagItem) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM tag_items WHERE id = :id");
				$success = $query->execute([':id' => $tagItem->getId()]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function removeTag(Tag $tag) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE tags SET IsDeleted = :isDeleted WHERE id = :id");
				$success = $query->execute(
				[
					':isDeleted' => $tag->getIsDeleted(),
					':id'        => $tag->getId(),
				]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function restoreTag(Tag $tag) : Tag
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE tags SET IsDeleted = :isDeleted WHERE id = :id");
				$query->execute(
				[
					':isDeleted' => $tag->getIsDeleted() ? 1 : 0,
					':id'        => $tag->getId(),
				]);

				return $tag;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getTagPlansInDateRange(DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo) : ?array
		{
			try
			{
				$tagPlans = null;

				$query = $this->ShopDb->conn->prepare("SELECT mpd.id AS tag_plan_day_id, mpd.date AS tag_plan_date, mpd.tag_id, mpd.order_item_status, m.name AS tag_name, m.IsDeleted AS tag_isDeleted FROM tag_plan_days AS mpd LEFT JOIN tags AS m ON (m.id = mpd.tag_id) WHERE mpd.date IS NOT NULL AND mpd.date >= :dateFrom AND mpd.date <= :dateTo ORDER BY mpd.date");

				$query->execute(
				[
					':dateFrom' => $dateFrom->format('Y-m-d'),
					':dateTo'   => $dateTo->format('Y-m-d'),
				]);

				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$tagPlans = [];

					foreach ($rows as $row)
					{
						$tag = createTag($row);
						$tagPlanDay = createTagPlanDay($row);
						$tagPlanDay->setTag($tag);

						$tagPlans[$tagPlanDay->getId()] = $tagPlanDay;
					}
				}

				return $tagPlans;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getTagPlanByDate(DateTime $date) : TagPlanDay
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT mpd.id AS tag_plan_day_id, mpd.date AS tag_plan_date, mpd.tag_id, mpd.order_item_status, m.name AS tag_name, m.IsDeleted AS tag_isDeleted FROM tag_plan_days AS mpd LEFT JOIN tags AS m ON (m.id = mpd.tag_id) WHERE mpd.date = :date");

				$query->execute([':date' => $date->format('Y-m-d')]);

				$row = $query->fetch(PDO::FETCH_ASSOC);

				if (empty($row))
				{
					$tagPlan = new TagPlanDay();
					$tagPlan->setDate($date);
				}
				else
				{
					$tag = createTag($row);
					$tagPlan = createTagPlanDay($row);
					$tagPlan->setTag($tag);
				}

				return $tagPlan;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function addTagPlanDay(TagPlanDay $tagPlanDay) : TagPlanDay
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO tag_plan_days (date, tag_id, order_item_status) VALUES (:date, :tagId, :orderItemStatus)");

				$query->execute(
				[
					':date'           => $tagPlanDay->getDateString(),
					'tagId'          => $tagPlanDay->getTagId(),
					'orderItemStatus' => $tagPlanDay->getOrderItemStatus(),
				]);

				$tagPlanDay->setId(intval($this->ShopDb->conn->lastInsertId()));

				return $tagPlanDay;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function updateTagPlanDay(TagPlanDay $tagPlanDay) : TagPlanDay
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE tag_plan_days SET tag_id = :tagId, order_item_status = :orderItemStatus WHERE id = :id");

				$success = $query->execute(
				[
					':tagId'          => $tagPlanDay->getTagId(),
					':orderItemStatus' => $tagPlanDay->getOrderItemStatus(),
					':id'              => $tagPlanDay->getId(),
				]);

				if (!$success)
				{
					throw new Exception("Error updating TagPlanDay");
				}

				return $tagPlanDay;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}
	}

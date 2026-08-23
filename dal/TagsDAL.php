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
				$query = $this->ShopDb->conn->prepare("INSERT INTO tags (name, IsDefaultInclude, IsDefaultExclude) VALUES (:name, :isDefaultInclude, :isDefaultExclude)");
				$query->execute(
				[
					':name'             => $tag->getName(),
					':isDefaultInclude' => $tag->getIsDefaultInclude() ? 1 : 0,
					':isDefaultExclude' => $tag->getIsDefaultExclude() ? 1 : 0,
				]);

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

				$query = $this->ShopDb->conn->prepare("SELECT t.id AS tag_id, t.name AS tag_name, t.IsDefaultInclude AS tag_isDefaultInclude, t.IsDefaultExclude AS tag_isDefaultExclude, m.id AS meal_id, m.name AS meal_name, m.IsDeleted AS meal_isDeleted FROM tags AS t LEFT JOIN meals_tags AS mt ON (mt.tag_id = t.id) LEFT JOIN meals AS m ON (m.id = mt.meal_id) WHERE t.id = :id AND (m.IsDeleted = 0 OR m.IsDeleted IS NULL) ORDER BY m.name");
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

						if (!is_null($row['meal_id']))
						{
							$meal = createMeal($row);

							if (!entityIsValid($meal))
							{
								throw new Exception("Invalid Meal. Meal ID #".$meal->getId());
							}

							$tag->addMeal($meal);
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

		public function getTagByName(string $tagName) : ?Tag
		{
			$tag = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS tag_id, name AS tag_name, IsDefaultInclude AS tag_isDefaultInclude, IsDefaultExclude AS tag_isDefaultExclude FROM tags WHERE name = :name LIMIT 1");
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

				// $query = $this->ShopDb->conn->prepare("SELECT t.id AS tag_id, t.name AS tag_name, m.id AS meal_id, m.name AS meal_name, m.IsDeleted AS meal_isDeleted FROM tags AS t LEFT JOIN meals_tags AS mt ON (mt.tag_id = t.id) LEFT JOIN meals AS m ON (m.id = mt.meal_id) WHERE m.IsDeleted = 0 ORDER BY t.name");
				$query = $this->ShopDb->conn->prepare("SELECT t.id AS tag_id, t.name AS tag_name, t.IsDefaultInclude AS tag_isDefaultInclude, t.IsDefaultExclude AS tag_isDefaultExclude FROM tags AS t ORDER BY t.name");
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

						// if (!is_null($row['meal_id']))
						// {
						// 	$meal = createMeal($row);
						// 	$tags[$row['tag_id']]->addMeal($meal);
						// }
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

		public function getAllMealsNotWithTag(int $tagId) : array
		{
			try
			{
				$meals = [];

				$query = $this->ShopDb->conn->prepare("SELECT m.id AS meal_id, m.name AS meal_name, m.IsDeleted AS meal_isDeleted FROM meals AS m WHERE m.id NOT IN (SELECT meal_id FROM meals_tags WHERE tag_id = :tag_id) AND m.IsDeleted = 0 ORDER BY m.name");
				$query->execute([':tag_id' => $tagId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					foreach ($rows as $row)
					{
						$meal = createMeal($row);
						$meals[$meal->getId()] = $meal;
					}
				}

				return $meals;
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

		public function getAllTagsNotWithMeal(int $mealId) : array
		{
			try
			{
				$tags = [];

				$query = $this->ShopDb->conn->prepare("SELECT t.id AS tag_id, t.name AS tag_name, t.IsDefaultInclude AS tag_isDefaultInclude, t.IsDefaultExclude AS tag_isDefaultExclude FROM tags AS t WHERE t.id NOT IN (SELECT tag_id FROM meals_tags WHERE meal_id = :meal_id) ORDER BY t.name");
				$query->execute([':meal_id' => $mealId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					foreach ($rows as $row)
					{
						$tag = createTag($row);
						$tags[$tag->getId()] = $tag;
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
				$query = $this->ShopDb->conn->prepare("UPDATE tags SET name = :name, IsDefaultInclude = :isDefaultInclude, IsDefaultExclude = :isDefaultExclude WHERE id = :id");
				$query->execute(
				[
					':name'             => $tag->getName(),
					':isDefaultInclude' => $tag->getIsDefaultInclude() ? 1 : 0,
					':isDefaultExclude' => $tag->getIsDefaultExclude() ? 1 : 0,
					':id'               => $tag->getId(),
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

		public function addTagToMeal(Tag $tag, Meal $meal) : void
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO meals_tags (meal_id, tag_id) VALUES (:mealId, :tagId)");
				$query->execute(
				[
					':mealId' => $meal->getId(),
					':tagId'  => $tag->getId(),
				]);
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

		public function removeTagFromMeal(Tag $tag, Meal $meal) : void
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM meals_tags WHERE meal_id = :mealId AND tag_id = :tagId");
				$query->execute(
				[
					':mealId' => $meal->getId(),
					':tagId'  => $tag->getId(),
				]);
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

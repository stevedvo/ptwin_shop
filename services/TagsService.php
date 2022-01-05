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

		public function tagNameExists(string $tagName) : bool
		{
			try
			{
				$tag = $this->dal->getTagByName($tagName);

				return ($tag instanceof Tag);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function tagNameIsUnique(string $tagName, int $tagId) : bool
		{
			try
			{
				$tag = $this->dal->getTagByName($tagName);

				if (!($tag instanceof Tag))
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

		public function getAllMealsNotWithTag(int $tagId) : array
		{
			try
			{
				return $this->dal->getAllMealsNotWithTag($tagId);
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

		public function addTagToMeal(Tag $tag, Meal $meal) : void
		{
			try
			{
				$this->dal->removeTagFromMeal($tag, $meal);
				$this->dal->addTagToMeal($tag, $meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeTagFromMeal(Tag $tag, Meal $meal) : void
		{
			try
			{
				$this->dal->removeTagFromMeal($tag, $meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		// public function removeTag(Tag $tag) : bool
		// {
		// 	try
		// 	{
		// 		$tag->setIsDeleted(true);

		// 		return $this->dal->removeTag($tag);
		// 	}
		// 	catch (Exception $e)
		// 	{
		// 		throw $e;
		// 	}
		// }

		// public function restoreTag(Tag $tag) : Tag
		// {
		// 	try
		// 	{
		// 		$tag->setIsDeleted(false);

		// 		return $this->dal->restoreTag($tag);
		// 	}
		// 	catch (Exception $e)
		// 	{
		// 		throw $e;
		// 	}
		// }
	}

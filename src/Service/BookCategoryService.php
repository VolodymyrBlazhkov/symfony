<?php

namespace App\Service;

use App\Entity\Category;
use App\Modal\BookCategoryListItem;
use App\Modal\BookCategoryListResponse;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Criteria;

class BookCategoryService
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function getCategories(): BookCategoryListResponse
    {
        $categories = $this->categoryRepository->findAllSortByTitle();
        $items = array_map(
            fn(Category $category) => new BookCategoryListItem(
                $category->getId(), $category->getTitle(), $category->getSlug()
            ),
            $categories
        );

        return new BookCategoryListResponse($items);
    }
}
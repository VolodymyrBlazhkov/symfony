<?php

namespace App\Service;

use App\Entity\Category;
use App\Exception\BookCategoryNotEmpty;
use App\Exception\CategoryExistWithSlugException;
use App\Modal\BookCategoryListItem;
use App\Modal\BookCategoryListResponse;
use App\Modal\BookCategoryUpdateRequest;
use App\Modal\IdResponse;
use App\Repository\CategoryRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookCategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private SluggerInterface $slugger
    ) {
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->categoryRepository->getById($id);
        $books = $this->categoryRepository->countBooksInCategory($category->getId());

        if ($books > 0) {
            throw new BookCategoryNotEmpty($books);
        }
        $this->categoryRepository->removeAndCommit($category);
    }

    public function createCategory(BookCategoryUpdateRequest $bookCategoryUpdateRequest): IdResponse
    {
        $category = new Category();
        $this->upserdCategory($category, $bookCategoryUpdateRequest);

        return new IdResponse($category->getId());
    }

    public function updateCategory(int $id, BookCategoryUpdateRequest $bookCategoryUpdateRequest): void
    {
        $this->upserdCategory($this->categoryRepository->getById($id), $bookCategoryUpdateRequest);
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

    private function upserdCategory(Category $category, BookCategoryUpdateRequest $bookCategoryUpdateRequest): void
    {
        $slug = $this->slugger->slug($bookCategoryUpdateRequest->getTitle());

        if ($this->categoryRepository->existBySlug($slug)) {
            throw new CategoryExistWithSlugException();
        }

        $category->setTitle($bookCategoryUpdateRequest->getTitle())->setSlug($slug);
        $this->categoryRepository->saveAndCommit($category);
    }
}
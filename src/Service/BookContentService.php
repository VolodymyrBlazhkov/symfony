<?php

namespace App\Service;

use App\Entity\BookContent;
use App\Modal\Author\CreateBookChapterContentRequest;
use App\Modal\BookChapterContent;
use App\Modal\BookChapterContentPage;
use App\Modal\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookContentRepository;

class BookContentService
{

    private const PAGE_LIMIT = 30;

    public function __construct(
        private BookContentRepository $bookContentRepository,
        private BookChapterRepository $bookChapterRepository
    ) {
    }

    public function createContent(CreateBookChapterContentRequest $request, int $chapterId): IdResponse
    {
        $content = new BookContent();

        $content->setChapter($this->bookChapterRepository->getBookById($chapterId));
        $this->saveContent($request, $content);

        return new IdResponse($content->getId());
    }

    public function updateContent(CreateBookChapterContentRequest $request, int $Id): void
    {
        $content = $this->bookContentRepository->getById($Id);
        $this->saveContent($request, $content);
    }

    public function deleteContent(int $Id): void
    {
        $content = $this->bookContentRepository->getById($Id);
        $this->bookContentRepository->removeAndCommit($content);
    }

    public function getAllContent(int $chapterId, int $page): BookChapterContentPage
    {
       return $this->getContent($chapterId, $page, false);
    }

    public function getPublishedContent(int $chapterId, int $page): BookChapterContentPage
    {
        return $this->getContent($chapterId, $page, true);
    }

    private function getContent(int $chapterId, int $page, bool $status): BookChapterContentPage
    {
        $items = [];
        $offset = PaginationUtils::calcOffset($page, self::PAGE_LIMIT);
        $paginator = $this->bookContentRepository->getPageByChapterId(
            $chapterId,
            $status,
            $offset,
            self::PAGE_LIMIT
        );

        foreach ($paginator as $item) {
            $items[] = (new BookChapterContent())
                ->setId($item->getId())
                ->setContent($item->getContent())
                ->setIsPublished($item->getIsPublished());
        }

        $total = $this->bookContentRepository->countByChapterId($chapterId, $status);

        return (new BookChapterContentPage())
            ->setTotal($total)
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(PaginationUtils::calcPages($total, self::PAGE_LIMIT))
            ->setItems($items);
    }

    private function saveContent(CreateBookChapterContentRequest $request, BookContent $content): void
    {
        $content->setContent($request->getContent())->setIsPublished($request->getIsPublished());
        $this->bookContentRepository->saveAndCommit($content);
    }
}
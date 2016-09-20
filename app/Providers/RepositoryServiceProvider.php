<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Entity;
use App\Models\Product;
use App\Models\Publisher;
use App\Models\SubCategory;
use App\Models\TreeCategory;
//use App\Repositories\Product\ProductRepository;
//use App\Repositories\TreeCategory\TreeCategoryRepository;
use App\Services\Cache\FullyCache;
use Illuminate\Support\ServiceProvider;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Faq;
use App\Models\News;
use App\Models\PhotoGallery;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Video;
use App\Models\Menu;
use App\Models\Slider;
use App\Models\Setting;
use App\Repositories\Article\ArticleRepository;
use App\Repositories\Article\CacheDecorator as ArticleCacheDecorator;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CacheDecorator as CategoryCacheDecorator;
use App\Repositories\TreeCategory\CacheDecorator as TreeCategoryCacheDecorator;
use App\Repositories\Page\PageRepository;
use App\Repositories\Page\CacheDecorator as PageCacheDecorator;
use App\Repositories\Faq\FaqRepository;
use App\Repositories\Faq\CacheDecorator as FaqCacheDecorator;
use App\Repositories\News\NewsRepository;
use App\Repositories\News\CacheDecorator as NewsCacheDecorator;
use App\Repositories\PhotoGallery\PhotoGalleryRepository;
use App\Repositories\PhotoGallery\CacheDecorator as PhotoGalleryCacheDecorator;
use App\Repositories\Project\ProjectRepository;
use App\Repositories\Project\CacheDecorator as ProjectCacheDecorator;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Tag\CacheDecorator as TagCacheDecorator;
use App\Repositories\Video\VideoRepository;
use App\Repositories\Video\CacheDecorator as VideoCacheDecorator;
use App\Repositories\Menu\MenuRepository;
use App\Repositories\Menu\CacheDecorator as MenuCacheDecorator;
use App\Repositories\Slider\SliderRepository;
use App\Repositories\Slider\CacheDecorator as SliderCacheDecorator;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\Setting\CacheDecorator as SettingCacheDecorator;

use App\Repositories\Entity\EntityRepository;
use App\Repositories\Entity\CacheDecorator as EntityCacheDecorator;

use App\Repositories\SubCategory\SubCategoryRepository;
use App\Repositories\SubCategory\CacheDecorator as SubCategoryCacheDecorator;


use App\Repositories\Book\BookRepository;
use App\Repositories\Book\CacheDecorator as BookCacheDecorator;


use App\Repositories\Author\AuthorRepository;
use App\Repositories\Author\CacheDecorator as AuthorCacheDecorator;

use App\Repositories\Publisher\PublisherRepository;
use App\Repositories\Publisher\CacheDecorator as PublisherCacheDecorator;

use App\Repositories\BookRate\BookRateRepository;
use App\Repositories\BookRate\CacheDecorator as BookRateCacheDecorator;



/**
 * Class RepositoryServiceProvider.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $app = $this->app;

        //dd($app['config']->get('fully.cache'));


        // entity
        $app->bind('App\Repositories\Entity\EntityInterface', function ($app) {

            $entity = new EntityRepository(
                new Entity()
            );

            //dd($app['config']->get('is_admin', false));

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $article = new EntityCacheDecorator(
                    $entity,
                    new FullyCache($app['cache'], 'entities')
                );
            }

            return $entity;
        });

        // article
        $app->bind('App\Repositories\Article\ArticleInterface', function ($app) {

            $article = new ArticleRepository(
                new Article()
            );

            //dd($app['config']->get('is_admin', false));

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $article = new ArticleCacheDecorator(
                    $article,
                    new FullyCache($app['cache'], 'articles')
                );
            }

            return $article;
        });

        // product
        $app->bind('App\Repositories\Product\ProductInterface', function ($app) {

            $product = new ProductRepository(
                new Product(), new Entity()
            );

            //dd($app['config']->get('is_admin', false));

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $product = new ProjectCacheDecorator(
                    $product,
                    new FullyCache($app['cache'], 'articles')
                );
            }

            return $product;
        });

        // category
        $app->bind('App\Repositories\Category\CategoryInterface', function ($app) {

            $category = new CategoryRepository(
                new Category()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $category = new CategoryCacheDecorator(
                    $category,
                    new FullyCache($app['cache'], 'categories')
                );
            }

            return $category;
        });

        // sub-category
        $app->bind('App\Repositories\SubCategory\SubCategoryInterface', function ($app) {

            $subCategory = new SubCategoryRepository(
                new SubCategory()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $subCategory = new SubCategoryCacheDecorator(
                    $subCategory ,
                    new FullyCache($app['cache'], 'subCategories')
                );
            }

            return $subCategory ;
        });


        // tree-category
        $app->bind('App\Repositories\TreeCategory\TreeCategoryInterface', function ($app) {

            $category = new TreeCategoryRepository(
                new TreeCategory()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $category = new TreeCategoryCacheDecorator(
                    $category,
                    new FullyCache($app['cache'], 'categories')
                );
            }

            return $category;
        });

        // page
        $app->bind('App\Repositories\Page\PageInterface', function ($app) {

            $page = new PageRepository(
                new Page()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $page = new PageCacheDecorator(
                    $page,
                    new FullyCache($app['cache'], 'pages')
                );
            }

            return $page;
        });

        // faq
        $app->bind('App\Repositories\Faq\FaqInterface', function ($app) {

            $faq = new FaqRepository(
                new Faq()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $faq = new FaqCacheDecorator(
                    $faq,
                    new FullyCache($app['cache'], 'faqs')
                );
            }

            return $faq;
        });

        // author
        $app->bind('App\Repositories\Author\AuthorInterface', function ($app) {

            $author = new AuthorRepository(
                new Author()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $author = new AuthorCacheDecorator(
                    $author,
                    new FullyCache($app['cache'], 'authors')
                );
            }

            return $author;
        });

        // publish
        $app->bind('App\Repositories\Publisher\PublisherInterface', function ($app) {

            $publisher = new PublisherRepository(
                new Publisher()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $publisher = new PublisherCacheDecorator(
                    $publisher,
                    new FullyCache($app['cache'], 'publisher')
                );
            }

            return $publisher;
        });

        // book
        $app->bind('App\Repositories\Book\BookInterface', function ($app) {

            $book = new BookRepository(
                new Book(), new Entity()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $book = new BookCacheDecorator(
                    $book,
                    new FullyCache($app['cache'], 'book')
                );
            }

            return $book;
        });

        // news
        $app->bind('App\Repositories\News\NewsInterface', function ($app) {

            $news = new NewsRepository(
                new News()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $news = new NewsCacheDecorator(
                    $news,
                    new FullyCache($app['cache'], 'pages')
                );
            }

            return $news;
        });

        // photo gallery
        $app->bind('App\Repositories\PhotoGallery\PhotoGalleryInterface', function ($app) {

            $photoGallery = new PhotoGalleryRepository(
                new PhotoGallery()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $photoGallery = new PhotoGalleryCacheDecorator(
                    $photoGallery,
                    new FullyCache($app['cache'], 'photo_galleries')
                );
            }

            return $photoGallery;
        });

        // project
        $app->bind('App\Repositories\Project\ProjectInterface', function ($app) {

            $project = new ProjectRepository(
                new Project()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $project = new ProjectCacheDecorator(
                    $project,
                    new FullyCache($app['cache'], 'projects')
                );
            }

            return $project;
        });

        // tag
        $app->bind('App\Repositories\Tag\TagInterface', function ($app) {

            $tag = new TagRepository(
                new Tag()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $tag = new TagCacheDecorator(
                    $tag,
                    new FullyCache($app['cache'], 'tags')
                );
            }

            return $tag;
        });

        // video
        $app->bind('App\Repositories\Video\VideoInterface', function ($app) {

            $video = new VideoRepository(
                new Video()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $video = new VideoCacheDecorator(
                    $video,
                    new FullyCache($app['cache'], 'pages')
                );
            }

            return $video;
        });

        // menu
        $app->bind('App\Repositories\Menu\MenuInterface', function ($app) {

            $menu = new MenuRepository(
                new Menu()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $menu = new MenuCacheDecorator(
                    $menu,
                    new FullyCache($app['cache'], 'menus')
                );
            }

            return $menu;
        });

        // slider
        $app->bind('App\Repositories\Slider\SliderInterface', function ($app) {

            $slider = new SliderRepository(
                new Slider()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $slider = new SliderCacheDecorator(
                    $slider,
                    new FullyCache($app['cache'], 'sliders')
                );
            }

            return $slider;
        });

        // setting
        $app->bind('App\Repositories\Setting\SettingInterface', function ($app) {

            $setting = new SettingRepository(
                new Setting()
            );

            if ($app['config']->get('fully.cache') === true && $app['config']->get('is_admin', false) == false) {
                $setting = new SettingCacheDecorator(
                    $setting,
                    new FullyCache($app['cache'], 'settings')
                );
            }

            return $setting;
        });
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CssController;
use App\Http\Controllers\BasicUiController;
use App\Http\Controllers\AdvanceUiController;
use App\Http\Controllers\ExtraComponentsController;
use App\Http\Controllers\BasicTableController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ChartController;
/** new controllers **/
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\BuyerUserController;

use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuyerTypeChannelController;
use App\Http\Controllers\ProductVariationTypeController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

// Route::prefix('superadmin')->group(function () { 
    Route::get('/superadmin-login', [LoginController::class, 'showLoginFormSuperadmin']);
    Route::post('/superadmin-login', [LoginController::class, 'postLoginFormSuperadmin'])->name('superadmin-login');
// });



Route::group(['middleware' => ['auth']], function () { 


    Route::get('/logout', [LoginController::class, 'logout']);
    /** country state city **/
    Route::post('api/fetch-states', [CompanyController::class, 'fetchState']);
    Route::post('api/fetch-cities', [CompanyController::class, 'fetchCity']);
    Route::get('image/{filename}', [DashboardController::class,'displayImage'])->name('image.displayImage');
    /** new routes start **/
    Route::prefix('superadmin')->middleware(['superadmin'])->group(function () { 
        Route::get('/', [DashboardController::class, 'dashboardSuperadminModern']);
        Route::get('/login', [LoginController::class, 'showLoginForm']);

    /** json task */

    Route::get('json_data', [DashboardController::class, 'json_data'])->name('json_data');



        /** company route */
        Route::resource('/company', CompanyController::class);
        // Route::get('/company/{id}/edit', [CompanyController::class,'edit'])->index('superadmin');
        Route::post('/company-import', [CompanyController::class,'companyImport'])->name('company-import');
        Route::get('/company-export', [CompanyController::class,'companyExport']);
        Route::get('/company/delete/{id}', [CompanyController::class,'destroy'])->name('company.destroy');
        
        /** company admin **/
        Route::get('/company-admin-create', [UserController::class, 'create'])->name('company-admin-create');
        Route::get('/company-admin-edit/{id}', [UserController::class, 'create'])->name('company-admin-edit');
        Route::post('/company-admin-update/{id}', [UserController::class, 'update'])->name('company-admin-update');
        Route::get('/company-admin-list', [UserController::class, 'index'])->name('company-admin-list');
        Route::post('/company-admin-create', [UserController::class, 'store'])->name('company-admin-create');
        Route::get('/company-admin-delete/{id}', [UserController::class, 'destroy'])->name('company-admin-delete');

        Route::get('/company-user-create', [UserController::class, 'usersCreate'])->name('superadmin.company-user-create');
        Route::get('/company-user-edit/{id}', [UserController::class, 'usersCreate'])->name('superadmin.company-user-edit');
        Route::post('/company-user-update/{id}', [UserController::class, 'usersUpdate'])->name('superadmin.company-user-update');
        Route::get('/company-user-list', [UserController::class, 'usersList'])->name('superadmin.company-user-list');
        Route::post('/company-user-create', [UserController::class, 'userStore'])->name('superadmin.company-user-create');
        Route::get('/company-user-delete/{id}', [UserController::class, 'destroyUser'])->name('superadmin.company-user-delete');

        /** new buyer routes start **/
        Route::resource('/buyer-type', BuyerController::class);
        Route::get('/buyer-type/delete/{id}', [BuyerController::class,'destroy'])->name('buyer-type.destroy');

               
        /** new products routes **/
        Route::resource('/product',ProductsController::class);
        Route::get('/product',[ProductsController::class,'index'])->name('superadmin.product.index');
        Route::get('/product/{$id}/edit',[ProductsController::class,'edit'])->name('superadmin.product.edit');
        Route::post('/product',[ProductsController::class,'store'])->name('superadmin.product.store');
        Route::patch('/product/{$id}',[ProductsController::class,'update'])->name('superadmin.product.update');
        Route::get('/product/destroy/{id}',[ProductsController::class,'destroy'])->name('superadmin.product.delete');
        Route::get('/product/imagedelete/{id}',[ProductsController::class,'imagedelete'])->name('superadmin.product.imagedelete');
        
        /** product category admin side **/
        Route::resource('/product-category',ProductCategoryController::class);
        Route::get('/product-category',[ProductCategoryController::class,'index'])->name('superadmin.product-category.index');
        Route::get('/product-category/{$id}/edit',[ProductCategoryController::class,'index'])->name('superadmin.product-category.edit');
        Route::post('/product-category',[ProductCategoryController::class,'store'])->name('superadmin.product-category.store');
        Route::put('/product-category/{$id}',[ProductCategoryController::class,'update'])->name('superadmin.product-category.update');
        Route::get('/product-category/destroy/{id}',[ProductCategoryController::class,'destroy'])->name('superadmin.product-category.delete');
        Route::post('/product-category-import',[ProductCategoryController::class,'productcategoryimport'])->name('superadmin.product-category-import');
        Route::get('/product-category-export/{type?}',[ProductCategoryController::class,'productcategoryexport'])->name('superadmin.product-category-export');

        /** product company mapping **/
        Route::resource('/product-mapping',ProductCompanyMapping::class);
        Route::get('/product-mapping/destroy/{id}',[ProductCompanyMapping::class,'destroy'])->name('product-mapping.delete');

        /** sub category admin side **/

        Route::resource('/product-subcategory',SubCategoryController::class);
        Route::get('/product-subcategory',[SubCategoryController::class,'index'])->name('superadmin.product-subcategory.index');
        Route::get('/product-subcategory/{$id}/edit',[SubCategoryController::class,'index'])->name('superadmin.product-subcategory.edit');
        Route::post('/product-subcategory',[SubCategoryController::class,'store'])->name('superadmin.product-subcategory.store');
        Route::put('/product-subcategory/{$id}',[SubCategoryController::class,'update'])->name('superadmin.product-subcategory.update');
        Route::get('/product-subcategory/destroy/{id}',[SubCategoryController::class,'destroy'])->name('superadmin.product-subcategory.delete');
        Route::post('/sub-category-import',[SubCategoryController::class,'subcategoryimport'])->name('superadmin.sub-category-import');
          
        /** new buyer registration routes start **/
        Route::resource('/buyer', BuyerUserController::class);
        Route::get('/buyer',[BuyerUserController::class,'index'])->name('superadmin.buyer.index');
        Route::get('/buyer/create',[BuyerUserController::class,'create'])->name('superadmin.buyer.create');
        Route::get('/buyer/{id}/edit',[BuyerUserController::class,'edit'])->name('superadmin.buyer.edit');
        Route::post('/buyer',[BuyerUserController::class,'store'])->name('superadmin.buyer.store');
        Route::patch('/buyer/{id}',[BuyerUserController::class,'update'])->name('superadmin.buyer.update');
        Route::get('/buyer/destroy/{id}',[BuyerUserController::class,'destroy'])->name('superadmin.buyer.delete');
        /** buyer import and export **/
        Route::post('/buyer/import', [BuyerUserController::class,'buyerImport'])->name('superadmin.buyer.import');
        Route::get('/buyer/export/{type}', [BuyerUserController::class,'buyerExport'])->name('superadmin.buyer.export');


        /** profile admin side **/

        Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('superadmin.profile-edit');

        Route::match(['post', 'patch'], '/profile-update/{id}', [ProfileController::class, 'update'])->name('superadmin.profile-update');

        
        /**  admin side Buyer Type Channel**/

        Route::resource('/buyer-type-channel',BuyerTypeChannelController::class);
        Route::get('/buyer-type-channel',[BuyerTypeChannelController::class,'index'])->name('superadmin.buyer-type-channel.index');
        Route::post('/buyer-type-channel',[BuyerTypeChannelController::class,'store'])->name('superadmin.buyer-type-channel.store');
        Route::get('/buyer-type-channel/{$id}/edit',[BuyerTypeChannelController::class,'index'])->name('superadmin.buyer-type-channel.edit');
        Route::get('/buyer-type-channel/destroy/{id}',[BuyerTypeChannelController::class,'destroy'])->name('superadmin.buyer-type-channel.delete');
        Route::put('/buyer-type-channel/{$id}',[BuyerTypeChannelController::class,'update'])->name('superadmin.buyer-type-channel.update');
        Route::post('/buyer-type-channel-import',[BuyerTypeChannelController::class,'buyertypechannelimport'])->name('superadmin.buyer-type-channel-import');

         /**  admin side Product Variation Type**/

         Route::resource('/product-variation-type',ProductVariationTypeController::class);
         Route::get('/product-variation-type',[ProductVariationTypeController::class,'index'])->name('superadmin.product-variation-type.index');
         Route::post('/product-variation-type',[ProductVariationTypeController::class,'store'])->name('superadmin.product-variation-type.store');
         Route::get('/product-variation-type/{$id}/edit',[ProductVariationTypeController::class,'index'])->name('superadmin.product-variation-type.edit');
         Route::get('/product-variation-type/destroy/{id}',[ProductVariationTypeController::class,'destroy'])->name('superadmin.product-variation-type.delete');
         Route::put('/product-variation-type/{$id}',[ProductVariationTypeController::class,'update'])->name('superadmin.product-variation-type.update');

         Route::post('/product-variation-type-import',[ProductVariationTypeController::class,'productvariationtypeimport'])->name('superadmin.product-variation-type-import');

    });

    Route::middleware(['companyadmin'])->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardModern']);
        /** company user **/
        Route::get('/company-user-create', [UserController::class, 'usersCreate'])->name('company-user-create');
        Route::get('/company-user-edit/{id}', [UserController::class, 'usersCreate'])->name('company-user-edit');
        Route::post('/company-user-update/{id}', [UserController::class, 'usersUpdate'])->name('company-user-update');
        Route::get('/company-user-list', [UserController::class, 'usersList'])->name('company-user-list');
        Route::post('/company-user-create', [UserController::class, 'userStore'])->name('company-user-create');
        Route::get('/company-user-delete/{id}', [UserController::class, 'destroyUser'])->name('company-user-delete');

        Route::get('/permision-edit', [UserController::class, 'usersPermission']);
        /** new products routes **/
        
         /** product category user side **/
         Route::resource('/product-category',ProductCategoryController::class);
         Route::get('/product-category/destroy/{id}',[ProductCategoryController::class,'destroy'])->name('product-category.delete');

        /** new products routes **/
        Route::resource('/product',ProductsController::class);
        Route::get('/product/destroy/{id}',[ProductsController::class,'destroy'])->name('product.delete');
        Route::get('/product/imagedelete/{id}',[ProductsController::class,'imagedelete'])->name('product.imagedelete');

        /** product category **/
        Route::resource('/product-category',ProductCategoryController::class);
        Route::get('/product-category/destroy/{id}',[ProductCategoryController::class,'destroy'])->name('product-category.delete');
        Route::post('/product-category-import',[ProductCategoryController::class,'productcategoryimport'])->name('product-category-import');
       

        /** sub category user side **/
        Route::resource('/product-subcategory',SubCategoryController::class);
        Route::get('/product-subcategory/destroy/{id}',[SubCategoryController::class,'destroy'])->name('product-subcategory.delete');

        
        Route::post('/sub-category-import',[SubCategoryController::class,'subcategoryimport'])->name('sub-category-import');
        // Route::get('/sub-category-export/{type?}',[SubCategoryController::class,'subCategoryexportFile'])->name('sub-category-export');


        /** user side Buyer Type Channel**/

        Route::resource('/buyer-type-channel',BuyerTypeChannelController::class);
        Route::get('/buyer-type-channel/destroy/{id}',[BuyerTypeChannelController::class,'destroy'])->name('buyer-type-channel.delete');
        Route::post('/buyer-type-channel-import',[BuyerTypeChannelController::class,'buyertypechannelimport'])->name('buyer-type-channel-import');

        Route::resource('/product-variation-type',ProductVariationTypeController::class);
        Route::get('/product-variation-type/destroy/{id}',[ProductVariationTypeController::class,'destroy'])->name('product-variation-type.delete');
        Route::post('/product-variation-type-import',[ProductVariationTypeController::class,'productvariationtypeimport'])->name('product-variation-type-import');
    
        
        /** new buyer registration routes start **/
        Route::resource('/buyer', BuyerUserController::class);

        
        /** profile user side **/

        Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('profile-edit');
    
        Route::match(['post', 'patch'], '/profile-update/{id}', [ProfileController::class, 'update'])->name('profile-update');
        
        Route::get('/buyer/destroy/{id}',[BuyerUserController::class,'destroy'])->name('buyer.delete');
        Route::post('/buyer/import', [BuyerUserController::class,'buyerImport'])->name('buyer.import');
        Route::get('/buyer/export/{type}', [BuyerUserController::class,'buyerExport'])->name('buyer.export');

    });


    Route::get('/sub-category-export/{type?}',[SubCategoryController::class,'subcategoryexport'])->name('sub-category-export');
    
    /** product category import and export **/
    
    Route::get('/product-category-export/{type?}',[ProductCategoryController::class,'productCategoryexport'])->name('product-category-export');

    Route::get('/product-variation-type-export/{type?}',[ProductVariationTypeController::class,'productvariationtypeexport'])->name('product-variation-type-export');

    Route::get('/buyer-type-channelexport/{type?}',[BuyerTypeChannelController::class,'buyertypechannel_export'])->name('buyer-type-channelexport');
     
    /** product import and export **/
    Route::post('/product-import', [ProductsController::class,'productImport']);
    Route::get('/product-export/{type?}', [ProductsController::class,'productExport'])->name('product-export');
    
    Route::post('/company-user-import', [UserController::class,'companyUserImport']);
    Route::get('/company-user-export/{type?}', [UserController::class,'companyUserExport'])->name('company-user-export');
    Route::get('/company-user-import', [UserController::class,'companyUserExport'])->name('company-user-import');

    
    /** state and city **/
    Route::post('api/user-fetch-states', [DashboardController::class, 'user_fetchState']);
    Route::post('api/user-fetch-cities', [DashboardController::class, 'user_fetchCity']);
    Route::post('api/fetch-subcategory', [DashboardController::class, 'product_fetchSubcategory']);
    

    /** new routes end **/



    // Dashboard Route
    // Route::get('/', [DashboardController::class, 'dashboardModern'])->middleware('verified');
    Route::get('/', [DashboardController::class, 'dashboardModern']);

    Route::get('/modern', [DashboardController::class, 'dashboardModern']);
    Route::get('/ecommerce', [DashboardController::class, 'dashboardEcommerce']);
    Route::get('/analytics', [DashboardController::class, 'dashboardAnalytics']);

    // Application Route
    Route::get('/app-email', [ApplicationController::class, 'emailApp']);
    Route::get('/app-email/content', [ApplicationController::class, 'emailContentApp']);
    Route::get('/app-chat', [ApplicationController::class, 'chatApp']);
    Route::get('/app-todo', [ApplicationController::class, 'todoApp']);
    Route::get('/app-kanban', [ApplicationController::class, 'kanbanApp']);
    Route::get('/app-file-manager', [ApplicationController::class, 'fileManagerApp']);
    Route::get('/app-contacts', [ApplicationController::class, 'contactApp']);
    Route::get('/app-calendar', [ApplicationController::class, 'calendarApp']);
    Route::get('/app-invoice-list', [ApplicationController::class, 'invoiceList']);
    Route::get('/app-invoice-view', [ApplicationController::class, 'invoiceView']);
    Route::get('/app-invoice-edit', [ApplicationController::class, 'invoiceEdit']);
    Route::get('/app-invoice-add', [ApplicationController::class, 'invoiceAdd']);
    Route::get('/eCommerce-products-page', [ApplicationController::class, 'ecommerceProduct']);
    Route::get('/eCommerce-pricing', [ApplicationController::class, 'eCommercePricing']);

    // User profile Route
    Route::get('/user-profile-page', [UserProfileController::class, 'userProfile']);

    // Page Route
    Route::get('/page-contact', [PageController::class, 'contactPage']);
    Route::get('/page-blog-list', [PageController::class, 'pageBlogList']);
    Route::get('/page-search', [PageController::class, 'searchPage']);
    Route::get('/page-knowledge', [PageController::class, 'knowledgePage']);
    Route::get('/page-knowledge/licensing', [PageController::class, 'knowledgeLicensingPage']);
    Route::get('/page-knowledge/licensing/detail', [PageController::class, 'knowledgeLicensingPageDetails']);
    Route::get('/page-timeline', [PageController::class, 'timelinePage']);
    Route::get('/page-faq', [PageController::class, 'faqPage']);
    Route::get('/page-faq-detail', [PageController::class, 'faqDetailsPage']);
    Route::get('/page-account-settings', [PageController::class, 'accountSetting']);
    Route::get('/page-blank', [PageController::class, 'blankPage']);
    Route::get('/page-collapse', [PageController::class, 'collapsePage']);

    // Media Route
    Route::get('/media-gallery-page', [MediaController::class, 'mediaGallery']);
    Route::get('/media-hover-effects', [MediaController::class, 'hoverEffect']);

    // User Route
    Route::get('/page-users-list', [UserController::class, 'usersList']);
    Route::get('/page-users-view', [UserController::class, 'usersView']);
    Route::get('/page-users-edit', [UserController::class, 'usersEdit']);



    // Card Route
    Route::get('/cards-basic', [CardController::class, 'cardBasic']);
    Route::get('/cards-advance', [CardController::class, 'cardAdvance']);
    Route::get('/cards-extended', [CardController::class, 'cardsExtended']);

    // Css Route
    Route::get('/css-typography', [CssController::class, 'typographyCss']);
    Route::get('/css-color', [CssController::class, 'colorCss']);
    Route::get('/css-grid', [CssController::class, 'gridCss']);
    Route::get('/css-helpers', [CssController::class, 'helpersCss']);
    Route::get('/css-media', [CssController::class, 'mediaCss']);
    Route::get('/css-pulse', [CssController::class, 'pulseCss']);
    Route::get('/css-sass', [CssController::class, 'sassCss']);
    Route::get('/css-shadow', [CssController::class, 'shadowCss']);
    Route::get('/css-animations', [CssController::class, 'animationCss']);
    Route::get('/css-transitions', [CssController::class, 'transitionCss']);

    // Basic Ui Route
    Route::get('/ui-basic-buttons', [BasicUiController::class, 'basicButtons']);
    Route::get('/ui-extended-buttons', [BasicUiController::class, 'extendedButtons']);
    Route::get('/ui-icons', [BasicUiController::class, 'iconsUI']);
    Route::get('/ui-alerts', [BasicUiController::class, 'alertsUI']);
    Route::get('/ui-badges', [BasicUiController::class, 'badgesUI']);
    Route::get('/ui-breadcrumbs', [BasicUiController::class, 'breadcrumbsUI']);
    Route::get('/ui-chips', [BasicUiController::class, 'chipsUI']);
    Route::get('/ui-chips', [BasicUiController::class, 'chipsUI']);
    Route::get('/ui-collections', [BasicUiController::class, 'collectionsUI']);
    Route::get('/ui-navbar', [BasicUiController::class, 'navbarUI']);
    Route::get('/ui-pagination', [BasicUiController::class, 'paginationUI']);
    Route::get('/ui-preloader', [BasicUiController::class, 'preloaderUI']);

    // Advance UI Route
    Route::get('/advance-ui-carousel', [AdvanceUiController::class, 'carouselUI']);
    Route::get('/advance-ui-collapsibles', [AdvanceUiController::class, 'collapsibleUI']);
    Route::get('/advance-ui-toasts', [AdvanceUiController::class, 'toastUI']);
    Route::get('/advance-ui-tooltip', [AdvanceUiController::class, 'tooltipUI']);
    Route::get('/advance-ui-dropdown', [AdvanceUiController::class, 'dropdownUI']);
    Route::get('/advance-ui-feature-discovery', [AdvanceUiController::class, 'discoveryFeature']);
    Route::get('/advance-ui-media', [AdvanceUiController::class, 'mediaUI']);
    Route::get('/advance-ui-modals', [AdvanceUiController::class, 'modalUI']);
    Route::get('/advance-ui-scrollspy', [AdvanceUiController::class, 'scrollspyUI']);
    Route::get('/advance-ui-tabs', [AdvanceUiController::class, 'tabsUI']);
    Route::get('/advance-ui-waves', [AdvanceUiController::class, 'wavesUI']);
    Route::get('/fullscreen-slider-demo', [AdvanceUiController::class, 'fullscreenSlider']);

    // Extra components Route
    Route::get('/extra-components-range-slider', [ExtraComponentsController::class, 'rangeSlider']);
    Route::get('/extra-components-sweetalert', [ExtraComponentsController::class, 'sweetAlert']);
    Route::get('/extra-components-nestable', [ExtraComponentsController::class, 'nestAble']);
    Route::get('/extra-components-treeview', [ExtraComponentsController::class, 'treeView']);
    Route::get('/extra-components-ratings', [ExtraComponentsController::class, 'ratings']);
    Route::get('/extra-components-tour', [ExtraComponentsController::class, 'tour']);
    Route::get('/extra-components-i18n', [ExtraComponentsController::class, 'i18n']);
    Route::get('/extra-components-highlight', [ExtraComponentsController::class, 'highlight']);

    // Basic Tables Route
    Route::get('/table-basic', [BasicTableController::class, 'tableBasic']);

    // Data Table Route
    Route::get('/table-data-table', [DataTableController::class, 'dataTable']);

    // Form Route
    Route::get('/form-elements', [FormController::class, 'formElement']);
    Route::get('/form-select2', [FormController::class, 'formSelect2']);
    Route::get('/form-validation', [FormController::class, 'formValidation']);
    Route::get('/form-masks', [FormController::class, 'masksForm']);
    Route::get('/form-editor', [FormController::class, 'formEditor']);
    Route::get('/form-file-uploads', [FormController::class, 'fileUploads']);
    Route::get('/form-layouts', [FormController::class, 'formLayouts']);
    Route::get('/form-wizard', [FormController::class, 'formWizard']);

    // Charts Route
    Route::get('/charts-chartjs', [ChartController::class, 'chartJs']);
    Route::get('/charts-chartist', [ChartController::class, 'chartist']);
    Route::get('/charts-sparklines', [ChartController::class, 'sparklines']);


    // locale route
    Route::get('lang/{locale}', [LanguageController::class, 'swap']);
});

// Authentication Route
Route::get('/user-login', [AuthenticationController::class, 'userLogin']);
Route::get('/user-register', [AuthenticationController::class, 'userRegister']);
Route::get('/user-forgot-password', [AuthenticationController::class, 'forgotPassword']);
Route::get('/user-lock-screen', [AuthenticationController::class, 'lockScreen']);

// Misc Route
Route::get('/page-404', [MiscController::class, 'page404']);
Route::get('/page-maintenance', [MiscController::class, 'maintenancePage']);
Route::get('/page-500', [MiscController::class, 'page500']);
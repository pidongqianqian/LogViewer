<?php namespace Arcanedev\LogViewer\Http\Routes;

use Arcanedev\Support\Routing\RouteRegistrar;

/**
 * Class     LogViewerRoute
 *
 * @package  Arcanedev\LogViewer\Http\Routes
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @codeCoverageIgnore
 */
class LogViewerRoute extends RouteRegistrar
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Map all routes.
     *
     * ailuoy修改,主要增加权限控制
     *
     */
    public function map()
    {
        if(config('log-viewer.middleware')){
            $this->name('log-viewer::')->group(function () {
                $middleware = config('log-viewer.middleware');
                // log-viewer::dashboard
                $this->get('/', 'LogViewerController@index')->name('dashboard')->middleware($middleware);

                $this->get('/folder', 'LogViewerController@folder')->name('folder')->middleware($middleware);

                $this->mapLogsRoutes();
            });
        }else{
            $this->name('log-viewer::')->group(function () {
                // log-viewer::dashboard
                $this->get('/', 'LogViewerController@index')->name('dashboard');

                $this->get('/folder', 'LogViewerController@folder')->name('folder');

                $this->mapLogsRoutes();
            });
        }
    }

    /**
     * Map the logs routes.
     * ailuoy修改做权限判断
     */
    private function mapLogsRoutes()
    {
        if(config('log-viewer.middleware')){
            $this->prefix('logs')->name('logs.')->group(function() {
                $middleware = config('log-viewer.middleware');
                $this->get('/', 'LogViewerController@listLogs')
                    ->name('list')->middleware($middleware); // log-viewer::logs.list

                $this->delete('delete', 'LogViewerController@delete')
                    ->name('delete')->middleware($middleware); // log-viewer::logs.delete

                $this->prefix('{date}')->group(function()  use($middleware) {
                    $this->get('/', 'LogViewerController@show')
                        ->name('show')->middleware($middleware); // log-viewer::logs.show

                    $this->get('download', 'LogViewerController@download')
                        ->name('download')->middleware($middleware); // log-viewer::logs.download

                    $this->get('{level}', 'LogViewerController@showByLevel')
                        ->name('filter')->middleware($middleware); // log-viewer::logs.filter
                });
            });
        }else{
            $this->prefix('logs')->name('logs.')->group(function() {
                $this->get('/', 'LogViewerController@listLogs')
                    ->name('list'); // log-viewer::logs.list

                $this->delete('delete', 'LogViewerController@delete')
                    ->name('delete'); // log-viewer::logs.delete

                $this->prefix('{date}')->group(function()  {
                    $this->get('/', 'LogViewerController@show')
                        ->name('show'); // log-viewer::logs.show

                    $this->get('download', 'LogViewerController@download')
                        ->name('download'); // log-viewer::logs.download

                    $this->get('{level}', 'LogViewerController@showByLevel')
                        ->name('filter'); // log-viewer::logs.filter
                });
            });
        }

    }
}

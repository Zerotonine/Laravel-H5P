<?php

namespace EscolaSoft\LaravelH5p\Http\Controllers;

use App\Http\Controllers\Controller;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use H5PEditorEndpoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Log;
use EscolaSoft\LaravelH5p\Eloquents\H5pLibrary;
use EscolaSoft\LaravelH5p\Eloquents\H5pResult;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentsUserData;
use EscolaSoft\LaravelH5p\Eloquents\H5pTmpfile;

class AjaxController extends Controller
{
    public function libraries(Request $request)
    {
        $machineName = $request->get('machineName');
        $major_version = $request->get('majorVersion');
        $minor_version = $request->get('minorVersion');

        $h5p = App::make('LaravelH5p');
        $core = $h5p::$core;
        $editor = $h5p::$h5peditor;

        //log($machineName);
        Log::debug('An informational message.'.$machineName.'====='.$h5p->get_language());
        if ($machineName) {
            $defaultLanguag = $editor->getLibraryLanguage($machineName, $major_version, $minor_version, $h5p->get_language());
            Log::debug('An informational message.'.$machineName.'====='.$h5p->get_language().'====='.$defaultLanguag);

            //   public function getLibraryData($machineName, $majorVersion, $minorVersion, $languageCode, $prefix = '', $fileDir = '', $defaultLanguage) {

            $editor->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $machineName, $major_version, $minor_version, $h5p->get_language(), '', $h5p->get_h5plibrary_url('', true), $defaultLanguag);  //$defaultLanguage
            // Log library load
            event(new H5pEvent('library', null, null, null, $machineName, $major_version.'.'.$minor_version));
        } else {
            // Otherwise retrieve all libraries
            $editor->ajax->action(H5PEditorEndpoints::LIBRARIES);
        }
    }

    public function singleLibrary(Request $request)
    {
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $request->get('_token'));
    }

    public function contentTypeCache(Request $request)
    {
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;

        $response = $editor->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE, $request->get('_token'));

        $installedLibraries = H5pLibrary::all();
        return; //early return, w/o content hub does not show up
        $response['libraries'] = collect($response['libraries'])->map(function ($lib) use ($installedLibraries) {
            $lib['installed'] = $installedLibraries->contains('name', $lib['machineName']);

            if ($lib['installed']) {
                $installedLibrary = $installedLibraries->filter(function ($installedLib) use ($lib) {
                    return $installedLib['name'] == $lib['machineName'];
                })->first();

                $lib['localMajorVersion'] = $installedLibrary->major_version;
                $lib['localMinorVersion'] = $installedLibrary->minor_version;
                $lib['localPatchVersion'] = $installedLibrary->patch_version;
            }

            return $lib;
        });

        return $response;
    }

    public function libraryInstall(Request $request)
    {
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        // $editor->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $request->get('_token'), $request->get('machineName'));
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $request->get('_token'), $request->get('id'));
    }

    // public function libraryUpload(Request $request)
    // {
    //     $filePath = $request->file('h5p')->getPathName();
    //     $h5p = App::make('LaravelH5p');
    //     $editor = $h5p::$h5peditor;
    //     $editor->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $request->get('_token'), $filePath, $request->get('contentId'));
    // }

    public function libraryUpload(Request $request, $nonce = null)
    {
        $filePath = $request->file('h5p')->getPathName();
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $request->get('_token'), $filePath, $request->get('contentId'), $nonce);
    }

    public function files(Request $request, $nonce = null, $contentId = null)
    {
        $filePath = $request->file('file');
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::FILES, $request->get('_token'), !$contentId ? $request->get('contentId') : $contentId);


        if ($nonce) {
            $last = H5pTmpfile::orderBy('id', 'desc')->first();
            $last->update([ 'nonce' => $nonce ]);
        }
    }

    public function filter(Request $request){
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::FILTER, $request->get('_token') ,$request->get('libraryParameters'));
    }

    public function __invoke(Request $request)
    {
        return response()->json($request->all());
    }

    public function finish(Request $request)
    {
        $input = $request->all();

        $ref = $request->headers->get('referer');
        $matches = []; //matches will be empty if bundleId is not in referer otherwise bundleid will be in $matches[2]
        preg_match("/embed.(\d+)\D+(\d+)/", $ref, $matches);

        $data = [
            'content_id' => $input['contentId'],
            'max_score' => $input['maxScore'],
            'score' => $input['score'],
            'opened' => $input['opened'],
            'finished' => $input['finished'],
            'time' => $input['finished'] - $input['opened'],
            'user_id' => \Auth::user()->id,
            'container_id' => count($matches) === 0 ? null : $matches[2]
        ];

        H5pResult::create($data);

        return response()->json([
            'success' => true,
        ]);
    }

    public function contentUserData(Request $request)
    {
        if($request->isMethod('get')) //Editor calls content-user-data endpoint with a GET req on loading a H5P for editing....WHY?!
            return;

        $input = $request->all();

        $ref = $request->header('referer');
        $matches = []; //matches[2] will be the bundleId/container_id, if no bundleId is present matches will be null
        preg_match("/embed.(\d+)\D+(\d+)/", $ref, $matches);

        $contentId = null;
        $bundleId = null;
        if(count($matches) === 0){
            $contentId = basename($ref);
        } else {
            $contentId = $matches[1];
            $bundleId = $matches[2];
        }


        $userData = H5pContentsUserData::where([
            'content_id' => $contentId,
            'data_id' => 'state',
            'sub_content_id' => 0,
            'user_id' => \Auth::user()->id,
            'container_id' => $bundleId
        ])->first();

        $data = [
            'content_id' => $contentId,
            'data_id' => 'state',
            'sub_content_id' => 0,
            'user_id' => \Auth::user()->id,
            'data' => $input['data'],
            'preload' => $input['preload'],
            'invalidate' => $input['invalidate'],
            'updated_at' => now(),
            'container_id' => $bundleId
        ];

        if (empty($userData)) {
            H5pContentsUserData::create($data);
        } else {
            $userData->update($data);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    //TODO: experimental
    public function storeTinCanStatements(Request $request){
        $all = $request->all();
    }
}

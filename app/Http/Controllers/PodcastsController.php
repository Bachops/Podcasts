<?php

namespace App\Http\Controllers;

use App\Models\PodcastModel;
use App\Models\PodcastSerieModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PodcastsController extends Controller
{
    //CREATING PODCAST
    public function podcast_create(Request $request)
    {
        $admin = $request->admin;
        $serie_name = $request->serie_name;
        $podcast_serie = PodcastSerieModel::where('cancelled', 0)->where('title', $serie_name)->first();
        if (!$podcast_serie) {
            $podcast_serie = new PodcastSerieModel;
            $podcast_serie->title = $serie_name;
            $podcast_serie->description = $request->serie_description;
            $podcast_serie->cancelled = 0;
            $podcast_serie->save();
        }
        $serie_id = $podcast_serie->id;
        $podcast = new PodcastModel;
        $podcast->title = $request->podcast_title;
        $podcast->description = $request->podcast_description;
        $podcast->serie_id = $serie_id;
        $podcast->cancelled = 0;

        Storage::makeDirectory('public/podcasts');

        if (request()->hasfile('podcast_media')) {
            $extention = request()->file('podcast_media')->getClientOriginalExtension();
            $podcast->extention = $extention;
        }
        $podcast->save();

        if (request()->hasfile('podcast_media')) {
            $new_name = $podcast->id . '.' . request()->file('podcast_media')->getClientOriginalExtension();
            Storage::disk('local')->putFileAs(
                '/public/podcasts',
                request()->file('podcast_media'),
                $new_name
            );
        }
        return response()->json(
            ['status' => "success", 'details' => $podcast],
            200
        );
    }

    //EDITING PODCAST
    public function podcast_edit(Request $request)
    {
        $podcast_id = $request->podcast_id;
        $podcast = PodcastModel::where('cancelled', 0)->where('id', $podcast_id)->first();
        if (!$podcast) {
            return response()->json(
                ['status' => "failure", 'details' => 'Podcast not found'],
                404
            );
        } else {
            if ($request->serie_name) {
                $serie_name = $request->serie_name;
                $podcast_serie = PodcastSerieModel::where('cancelled', 0)->where('title', $serie_name)->first();
                if (!$podcast_serie) {
                    $podcast_serie = new PodcastSerieModel;
                    $podcast_serie->title = $serie_name;
                    if ($request->serie_description) {
                        $podcast_serie->description = $request->serie_description;
                    } else {
                        $podcast_serie->description = 'To be filled';
                    }
                    $podcast_serie->cancelled = 0;
                    $podcast_serie->save();
                }
                $serie_id = $podcast_serie->id;
                $podcast->serie_id = $serie_id;
            }
            if ($request->podcast_title) {
                $podcast->title = $request->podcast_title;
            }
            if (request()->hasfile('podcast_media')) {
                $extention = request()->file('podcast_media')->getClientOriginalExtension();
                $podcast->extention = $extention;
            }
            $podcast->save();
            if (request()->hasfile('podcast_media')) {
                $new_name = $podcast->id . '.' . request()->file('podcast_media')->getClientOriginalExtension();
                Storage::disk('local')->putFileAs(
                    '/public/podcasts',
                    request()->file('podcast_media'),
                    $new_name
                );
            }
            return response()->json(
                ['status' => "success", 'details' => $podcast],
                200
            );
        }
    }

    //DELETING PODCAST (only removing them from user interface keep backup in database)
    public function podcast_delete(Request $request)
    {
        $podcast_id = $request->podcast_id;
        $podcast = PodcastModel::where('cancelled', 0)->where('id', $podcast_id)->first();
        if (!$podcast) {
            return response()->json(
                ['status' => "failure", 'details' => 'Podcast not found'],
                404
            );
        } else {
            $podcast->cancelled = 1;
            $podcast->save();
            return response()->json(
                ['status' => "success", 'details' => 'podcast successfully removed'],
                200
            );
        }
    }

    //LIST OF PODCASTS WITH THEIR RELATIVE SERIE
    public function list_podcasts(Request $request)
    {
        $podcasts = PodcastModel::where('cancelled', 0)->with('serie')->get();
        if ($podcasts) {
            return response()->json(
                ['status' => "success", 'details' => $podcasts],
                200
            );
        } else {
            return response()->json(
                ['status' => "failure", 'details' => 'No podcasts found'],
                404
            );
        }
    }
}

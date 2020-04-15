<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuoteResource;
use App\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $quote =  Quote::with(['user'])->latest()->paginate(5);

        return QuoteResource::collection($quote);
    }

    public function show($id)
    {
        $quote = Quote::find($id);
        if(is_null($quote))
        {   
            return response()->json([
                "error" => "Resource not found"
            ], 404);
        }
        return new QuoteResource($quote);
    }

    public function update(Request $request, Quote $quote)
    {
        $this->authorize('update', $quote);
        $quote->update([
            'message' => $request->message
        ]);
        
        return new QuoteResource($quote);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'message' => 'required'
        ]);

        $quote = Quote::create([
            'user_id' => auth()->id(),
            'message' => $request->message
        ]);

        return new QuoteResource($quote);
    }

    public function destroy(Quote $quote)
    {
        $this->authorize('delete', $quote);
        $quote->delete();

        return response()->json([
            'message' => 'Quote deleted'
        ]);
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview — {{ $blog->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .preview-banner {
            background: #1c1c1e; color: #fff; text-align: center;
            padding: .6rem; font-size: 13px;
        }
        .preview-banner strong { color: #aa8038; }
        article { max-width: 760px; margin: 2.5rem auto; background: #fff; border-radius: 12px; padding: 2.5rem; box-shadow: 0 6px 18px rgba(0,0,0,.06); }
        article img.cover { width: 100%; border-radius: 10px; margin-bottom: 1.5rem; object-fit: cover; max-height: 380px; }
        article .meta { color: #6c757d; font-size: 13px; margin-bottom: 1rem; }
        article .content { line-height: 1.75; color: #222; }
        article .content img { max-width: 100%; height: auto; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="preview-banner">
        <strong>PREVIEW MODE</strong> — this is how "{{ $blog->title }}" will look once published. Status: <strong class="text-capitalize">{{ $blog->status }}</strong>
    </div>

    <article>
        @if ($blog->featured_image)
            <img src="{{ url('public/storage/'.$blog->featured_image) }}" class="cover" alt="{{ $blog->title }}">
        @endif

        @if ($blog->category)
            <span class="badge" style="background:#f4ead9;color:#aa8038;">{{ $blog->category->name }}</span>
        @endif

        <h1 class="mt-3 mb-2 fw-bold">{{ $blog->title }}</h1>

        <div class="meta">
            By {{ $blog->author->name ?? 'Devotion Technology' }}
            &middot; {{ $blog->publish_date?->format('F d, Y') ?? 'Not scheduled' }}
            @if ($blog->reading_time) &middot; {{ $blog->reading_time }} min read @endif
        </div>

        @if ($blog->short_description)
            <p class="lead">{{ $blog->short_description }}</p>
        @endif

        <div class="content">
            {!! $blog->content !!}
        </div>

        @if ($blog->tags->isNotEmpty())
            <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2">
                @foreach ($blog->tags as $tag)
                    <span class="badge bg-light text-dark border">{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif
    </article>
</body>
</html>

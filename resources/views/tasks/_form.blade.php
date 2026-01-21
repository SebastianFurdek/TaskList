<div class="mb-3">
    <label for="title" class="form-label">Názov</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', isset($task) ? $task->title : '') }}" required placeholder="Napíšte názov úlohy">
</div>

<div class="mb-3">
    <label for="description" class="form-label">Popis</label>
    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Krátky popis úlohy">{{ old('description', isset($task) ? $task->description : '') }}</textarea>
    <div class="form-text">Maximálne 1000 znakov. (Server-side validačné pravidlá aplikované.)</div>
</div>

<div class="mb-3">
    <label for="project_id" class="form-label">Priradiť k projektu</label>
    <select name="project_id" id="project_id" class="form-select">
        <option value="">-- Bez projektu --</option>
        @if(isset($projects) && $projects->count())
            @foreach($projects as $projectOption)
                <option value="{{ $projectOption->id }}" {{ old('project_id', isset($task) && $task->project_id ? $task->project_id : '') == $projectOption->id ? 'selected' : '' }}>
                    {{ $projectOption->name }}
                </option>
            @endforeach
        @endif
    </select>
    @error('project_id')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="categories" class="form-label">Kategórie</label>
    <div class="d-flex flex-wrap gap-2">
        @if(isset($categories) && $categories->count())
            @php
                $selected = old('categories', isset($task) ? $task->categories->pluck('id')->toArray() : []);
            @endphp
            @foreach($categories as $catOption)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $catOption->id }}" id="cat_{{ $catOption->id }}"
                        {{ in_array($catOption->id, $selected) ? 'checked' : '' }}>
                    <label class="form-check-label" for="cat_{{ $catOption->id }}">{{ $catOption->name }}</label>
                </div>
            @endforeach
        @else
            <div class="text-muted">Zatiaľ žiadne kategórie.</div>
        @endif
    </div>
    <div class="form-text">Vyberte všetky relevantné kategórie.</div>
    @error('categories')<div class="text-danger small">{{ $message }}</div>@enderror
    @error('categories.*')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="due_date" class="form-label">Termín</label>
    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
</div>

<button type="submit" class="btn btn-primary">Uložiť</button>

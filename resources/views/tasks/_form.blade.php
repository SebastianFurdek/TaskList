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
    <label for="due_date" class="form-label">Termín</label>
    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="completed" id="completed" class="form-check-input" value="1" {{ old('completed', isset($task) ? $task->completed : false) ? 'checked' : '' }}>
    <label for="completed" class="form-check-label">Dokončené</label>
</div>

<button type="submit" class="btn btn-primary">Uložiť</button>

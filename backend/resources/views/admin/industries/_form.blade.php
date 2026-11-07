<div class="space-y-4 rounded-lg border border-stone-200 bg-white p-5">
    <x-admin.input name="name" label="Tên ngành nghề" :value="$industry->name" required />
    <x-admin.select name="parent_id" label="Ngành cha" :options="$parents" :value="$industry->parent_id" placeholder="Không có (ngành gốc)" />
    <x-admin.select name="status" label="Trạng thái" :options="\App\Models\Industry::STATUS_LABELS" :value="$industry->status ?? 'active'" required />
    <x-admin.textarea name="description" label="Mô tả" :value="$industry->description" />
    <div class="flex gap-2">
        <x-admin.button>Lưu</x-admin.button>
        <x-admin.button variant="secondary" :href="route('admin.industries.index')">Hủy</x-admin.button>
    </div>
</div>

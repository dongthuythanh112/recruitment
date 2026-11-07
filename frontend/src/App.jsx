import { useEffect, useState } from 'react'
import {
  getTechnologies,
  createTechnology,
  deleteTechnology,
} from './services/technologyService'
import './App.css'

function App() {
  const [items, setItems] = useState([])
  const [name, setName] = useState('')
  const [description, setDescription] = useState('')
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [saving, setSaving] = useState(false)

  const loadItems = async () => {
    setLoading(true)
    setError('')

    try {
      const data = await getTechnologies()
      setItems(data)
    } catch {
      setError('Không thể tải dữ liệu. Hãy kiểm tra Laravel API và MySQL.')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadItems()
  }, [])

  const handleSubmit = async (event) => {
    event.preventDefault()

    if (!name.trim()) {
      setError('Vui lòng nhập tên công nghệ.')
      return
    }

    setSaving(true)
    setError('')

    try {
      await createTechnology({
        name: name.trim(),
        description: description.trim() || null,
      })

      setName('')
      setDescription('')
      await loadItems()
    } catch (err) {
      setError(
        err.response?.data?.message ||
          'Không thể thêm dữ liệu. Vui lòng thử lại.'
      )
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (id) => {
    if (!window.confirm('Sếp có chắc muốn xóa bản ghi này?')) return

    setError('')

    try {
      await deleteTechnology(id)
      await loadItems()
    } catch {
      setError('Không thể xóa bản ghi.')
    }
  }

  return (
    <main className="container">
      <header className="page-header">
        <p className="eyebrow">REACT + LARAVEL + MYSQL</p>
        <h1>Recruitment Base</h1>
        <p>Kiểm tra kết nối frontend, API và database.</p>
      </header>

      <section className="panel">
        <h2>Thêm dữ liệu thử nghiệm</h2>

        <form onSubmit={handleSubmit} className="form">
          <label htmlFor="name">Tên công nghệ</label>
          <input
            id="name"
            value={name}
            onChange={(event) => setName(event.target.value)}
            placeholder="Ví dụ: ReactJS"
            required
          />

          <label htmlFor="description">Mô tả</label>
          <textarea
            id="description"
            value={description}
            onChange={(event) => setDescription(event.target.value)}
            placeholder="Mô tả ngắn"
            rows={3}
          />

          <button type="submit" disabled={saving}>
            {saving ? 'Đang lưu...' : 'Thêm dữ liệu'}
          </button>
        </form>
      </section>

      <section className="panel">
        <div className="section-heading">
          <h2>Dữ liệu từ MySQL</h2>
          <button type="button" className="secondary" onClick={loadItems}>
            Làm mới
          </button>
        </div>

        {error && <p className="error">{error}</p>}

        {loading ? (
          <p>Đang tải dữ liệu...</p>
        ) : items.length === 0 ? (
          <p>Chưa có dữ liệu. Hãy thêm bản ghi đầu tiên.</p>
        ) : (
          <div className="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Tên</th>
                  <th>Mô tả</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                {items.map((item) => (
                  <tr key={item.id}>
                    <td>{item.id}</td>
                    <td>{item.name}</td>
                    <td>{item.description || '—'}</td>
                    <td>
                      <button
                        type="button"
                        className="danger"
                        onClick={() => handleDelete(item.id)}
                      >
                        Xóa
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </section>
    </main>
  )
}

export default App
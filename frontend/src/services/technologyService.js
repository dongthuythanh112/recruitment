import api from './api'

export const getTechnologies = async () => {
  const response = await api.get('/technologies')
  return response.data
}

export const createTechnology = async (data) => {
  const response = await api.post('/technologies', data)
  return response.data
}

export const updateTechnology = async (id, data) => {
  const response = await api.put(`/technologies/${id}`, data)
  return response.data
}

export const deleteTechnology = async (id) => {
  await api.delete(`/technologies/${id}`)
}

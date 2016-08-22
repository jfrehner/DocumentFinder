import { Schema, arrayOf } from 'normalizr'

export const document = new Schema('documents')
export const arrayOfDocuments = arrayOf(document)

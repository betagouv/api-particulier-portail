import { Brand } from 'src/types';

export type ApiKeyDTO = Brand<string, 'ApiKeyDTO'>;

export type KeyDTO = {
  id: ApiKeyDTO;
};

import { Brand } from 'src/types';

export type ApiKey = Brand<string, 'ApiKey'>;

export type Key = {
  id: ApiKey;
};

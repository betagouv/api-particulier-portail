import { Brand } from 'src/types';

export type ApplicationId = Brand<string, 'ApplicationId'>;

export type Application = {
  id: ApplicationId;
  name: string;
};

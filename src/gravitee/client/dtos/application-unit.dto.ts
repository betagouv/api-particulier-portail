import { Brand } from 'src/types';

export type ApplicationDTOId = Brand<string, 'ApplicationDTOId'>;

export type ApplicationDTO = {
  id: ApplicationDTOId;
  name: string;
};
